<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteAuditApiRun;
use App\Models\WebsiteReport;
use App\Models\WebsiteReportFinding;
use App\Models\WebsiteReportPage;
use App\Notifications\WebsiteReportReady;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebsiteAuditRunner
{
    public function __construct(
        private readonly SafeWebsiteUrl $safeUrl,
        private readonly WebsiteFetcher $fetcher,
        private readonly WebsiteAnalyzer $analyzer,
        private readonly PageSpeedClient $pageSpeed,
        private readonly W3cValidatorClient $w3c,
        private readonly MdnObservatoryClient $observatory,
        private readonly CruxClient $crux,
        private readonly BrowserlessClient $browserless,
        private readonly ReportScorer $scorer,
        private readonly PdfReportGenerator $pdf
    ) {}

    public function run(WebsiteReport $report): void
    {
        $report->update([
            'status' => 'processing',
            'current_stage' => 'Validating website',
            'progress' => 5,
            'started_at' => now(),
            'error_message' => null,
        ]);

        $url = $this->safeUrl->normalize($report->requested_url);
        $this->safeUrl->assertPublic($url);

        $this->stage($report, 'Reading the website', 12);
        $fetchStarted = microtime(true);
        $fetchRun = WebsiteAuditApiRun::create([
            'website_report_id' => $report->id,
            'provider' => 'WebIgnitors crawler',
            'operation' => 'fetch-html',
            'status' => 'running',
        ]);

        try {
            $fetched = $this->fetcher->fetch($url);
            $fetchRun->update([
                'status' => 'completed',
                'http_status' => $fetched['status'],
                'duration_ms' => (int) ((microtime(true) - $fetchStarted) * 1000),
                'response_summary' => [
                    'final_url' => $fetched['url'],
                    'html_bytes' => strlen($fetched['body']),
                ],
            ]);
        } catch (\Throwable $exception) {
            $fetchRun->update([
                'status' => 'failed',
                'duration_ms' => (int) ((microtime(true) - $fetchStarted) * 1000),
                'error_message' => $exception->getMessage(),
            ]);
            throw $exception;
        }

        $this->stage($report, 'Analysing content, design and conversion paths', 22);
        $analysis = $this->analyzer->analyze($fetched['body'], $fetched['url'], $fetched['headers']);
        $page = WebsiteReportPage::updateOrCreate(
            ['website_report_id' => $report->id, 'url' => $fetched['url']],
            [
                'title' => data_get($analysis, 'meta.title'),
                'http_status' => $fetched['status'],
                'meta' => $analysis['meta'],
            ]
        );

        $report->update([
            'final_url' => $fetched['url'],
            'domain' => strtolower((string) parse_url($fetched['url'], PHP_URL_HOST)),
            'website_title' => data_get($analysis, 'meta.title') ?: $report->domain,
        ]);

        $mobile = null;
        $desktop = null;
        if (config('audit.pagespeed.enabled')) {
            $this->stage($report, 'Running mobile Lighthouse audit', 32);
            $mobile = $this->attempt(fn () => $this->pageSpeed->audit($report, $fetched['url'], 'mobile'));

            $this->stage($report, 'Running desktop Lighthouse audit', 50);
            $desktop = $this->attempt(fn () => $this->pageSpeed->audit($report, $fetched['url'], 'desktop'));
        }

        $this->stage($report, 'Checking markup and security', 68);
        $w3c = config('audit.w3c.enabled')
            ? $this->attempt(fn () => $this->w3c->audit($report, $fetched['url']))
            : null;
        $observatory = config('audit.observatory.enabled')
            ? $this->attempt(fn () => $this->observatory->audit($report, $report->domain))
            : null;
        $crux = $this->attempt(fn () => $this->crux->audit($report, $fetched['url']));

        $this->stage($report, 'Preparing visual evidence', 76);
        $mobilePath = $this->saveScreenshot($report, 'mobile', $fetched['url'], $mobile['screenshot'] ?? null);
        $desktopPath = $this->saveScreenshot($report, 'desktop', $fetched['url'], $desktop['screenshot'] ?? null);

        $scores = $this->scorer->score($analysis, $mobile, $desktop, $w3c, $observatory);
        $findings = array_merge(
            $analysis['findings'],
            $mobile['findings'] ?? [],
            $desktop['findings'] ?? [],
            $w3c['findings'] ?? [],
            $observatory['findings'] ?? []
        );
        $findings = $this->sortFindings($findings);

        $page->update([
            'scores' => $scores,
            'metrics' => [
                'mobile' => $mobile['metrics'] ?? null,
                'desktop' => $desktop['metrics'] ?? null,
                'field_data' => $crux,
                'html_validation' => $w3c ? array_diff_key($w3c, ['findings' => true]) : null,
                'security' => $observatory ? array_diff_key($observatory, ['findings' => true]) : null,
            ],
            'audit_data' => [
                'mobile' => $this->auditSummary($mobile),
                'desktop' => $this->auditSummary($desktop),
            ],
            'mobile_screenshot_path' => $mobilePath,
            'desktop_screenshot_path' => $desktopPath,
        ]);

        $report->findings()->delete();
        foreach ($findings as $position => $finding) {
            WebsiteReportFinding::create([
                ...$finding,
                'website_report_id' => $report->id,
                'website_report_page_id' => $page->id,
                'position' => $position + 1,
            ]);
        }

        $severityCounts = collect($findings)->countBy('severity')->all();
        $topRecommendations = collect($findings)
            ->take(10)
            ->map(fn (array $finding) => [
                'title' => $finding['title'],
                'category' => $finding['category'],
                'severity' => $finding['severity'],
                'impact' => $finding['impact'],
                'effort' => $finding['effort'],
                'recommendation' => $finding['recommendation'],
            ])
            ->values()
            ->all();
        $summary = $this->summary($scores, $severityCounts, count($findings));

        $report->update([
            'scores' => $scores,
            'summary' => $summary,
            'top_recommendations' => $topRecommendations,
            'tool_versions' => [
                'lighthouse_mobile' => $mobile['version'] ?? null,
                'lighthouse_desktop' => $desktop['version'] ?? null,
                'webignitors_analyser' => '1.0',
                'report_schema' => '1.0',
            ],
        ]);

        $this->stage($report, 'Building your branded PDF', 90);
        $dataPath = "reports/{$report->uuid}/report-data.json";
        Storage::disk('local')->put($dataPath, json_encode([
            'report' => [
                'uuid' => $report->uuid,
                'url' => $report->final_url,
                'generated_at' => now()->toIso8601String(),
                'scores' => $scores,
                'summary' => $summary,
            ],
            'page' => $page->fresh()->toArray(),
            'findings' => $report->findings()->orderBy('position')->get()->toArray(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $report->update(['data_path' => $dataPath]);
        $pdfPath = $this->pdf->generate($report->fresh());

        $report->update([
            'status' => 'completed',
            'current_stage' => 'Report ready',
            'progress' => 100,
            'pdf_path' => $pdfPath,
            'completed_at' => now(),
        ]);

        try {
            $report->user->notify(new WebsiteReportReady($report->fresh()));
        } catch (\Throwable $exception) {
            Log::warning('Website report ready email failed.', [
                'report_id' => $report->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function stage(WebsiteReport $report, string $stage, int $progress): void
    {
        $report->update(['current_stage' => $stage, 'progress' => $progress]);
    }

    private function attempt(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $exception) {
            Log::notice('Optional website audit source failed.', ['error' => $exception->getMessage()]);

            return null;
        }
    }

    private function saveScreenshot(WebsiteReport $report, string $device, string $url, ?string $lighthouseData): ?string
    {
        $binary = $this->attempt(fn () => $this->browserless->screenshot($report, $url, $device));
        $extension = 'jpg';

        if (! is_string($binary) || $binary === '') {
            $decoded = $this->decodeDataUri($lighthouseData);
            if (! $decoded) {
                return null;
            }
            [$binary, $extension] = $decoded;
        }

        $path = "reports/{$report->uuid}/{$device}.{$extension}";
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private function decodeDataUri(?string $data): ?array
    {
        if (! $data || ! preg_match('#^data:image/(png|jpeg);base64,(.+)$#s', $data, $matches)) {
            return null;
        }

        $decoded = base64_decode($matches[2], true);

        return $decoded === false ? null : [$decoded, $matches[1] === 'png' ? 'png' : 'jpg'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $findings
     * @return array<int, array<string, mixed>>
     */
    private function sortFindings(array $findings): array
    {
        $severity = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4];
        $impact = ['high' => 0, 'medium' => 1, 'low' => 2];

        usort($findings, function (array $left, array $right) use ($severity, $impact): int {
            return [
                $severity[$left['severity']] ?? 5,
                $impact[$left['impact']] ?? 3,
                $left['effort'] === 'small' ? 0 : 1,
            ] <=> [
                $severity[$right['severity']] ?? 5,
                $impact[$right['impact']] ?? 3,
                $right['effort'] === 'small' ? 0 : 1,
            ];
        });

        return array_values($findings);
    }

    /**
     * @param  array<string, int>  $scores
     * @param  array<string, int>  $severityCounts
     * @return array<string, mixed>
     */
    private function summary(array $scores, array $severityCounts, int $findingCount): array
    {
        $overall = $scores['overall'];
        $rating = match (true) {
            $overall >= 90 => 'Excellent digital foundation',
            $overall >= 78 => 'Strong foundation with focused opportunities',
            $overall >= 65 => 'Promising foundation that needs refinement',
            $overall >= 50 => 'Significant improvement opportunity',
            default => 'Priority rebuild and optimisation recommended',
        };

        $strengths = collect($scores)
            ->except('overall')
            ->sortDesc()
            ->take(3)
            ->keys()
            ->map(fn (string $key) => ucfirst($key))
            ->values()
            ->all();

        return [
            'rating' => $rating,
            'finding_count' => $findingCount,
            'severity_counts' => $severityCounts,
            'strengths' => $strengths,
            'narrative' => "The audit identified {$findingCount} actionable observations. The strongest areas are ".implode(', ', $strengths).'. Prioritise critical and high-impact findings before lower-risk refinements.',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $audit
     * @return array<string, mixed>|null
     */
    private function auditSummary(?array $audit): ?array
    {
        if (! $audit) {
            return null;
        }

        return [
            'scores' => $audit['scores'] ?? null,
            'version' => $audit['version'] ?? null,
            'fetch_time' => $audit['fetch_time'] ?? null,
            'final_url' => $audit['final_url'] ?? null,
            'warnings' => $audit['warnings'] ?? [],
        ];
    }
}
