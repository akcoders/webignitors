<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteAuditApiRun;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PageSpeedClient
{
    /**
     * @return array<string, mixed>
     */
    public function audit(WebsiteReport $report, string $url, string $strategy): array
    {
        if (! config('audit.pagespeed.enabled')) {
            throw new RuntimeException('PageSpeed auditing is disabled.');
        }

        $started = microtime(true);
        $run = WebsiteAuditApiRun::create([
            'website_report_id' => $report->id,
            'provider' => 'Google PageSpeed Insights',
            'operation' => "lighthouse-{$strategy}",
            'status' => 'running',
        ]);

        try {
            $query = http_build_query(array_filter([
                'url' => $url,
                'strategy' => $strategy,
                'locale' => 'en',
                'key' => config('audit.pagespeed.api_key'),
            ]));

            foreach (['performance', 'accessibility', 'best-practices', 'seo'] as $category) {
                $query .= '&category='.rawurlencode($category);
            }

            $response = Http::acceptJson()
                ->timeout(config('audit.pagespeed.timeout'))
                ->connectTimeout(15)
                ->retry(1, 1000, throw: false)
                ->get(config('audit.pagespeed.endpoint').'?'.$query);

            if (! $response->successful()) {
                throw new RuntimeException("PageSpeed returned HTTP {$response->status()}.");
            }

            $result = $this->normalize($response->json(), $strategy);

            $run->update([
                'status' => 'completed',
                'http_status' => $response->status(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'response_summary' => [
                    'strategy' => $strategy,
                    'scores' => $result['scores'],
                    'findings' => count($result['findings']),
                ],
            ]);

            return $result;
        } catch (\Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalize(array $payload, string $strategy): array
    {
        $lighthouse = $payload['lighthouseResult'] ?? null;
        if (! is_array($lighthouse)) {
            throw new RuntimeException('PageSpeed did not return a Lighthouse result.');
        }

        $scores = [];
        $auditCategories = [];

        foreach (($lighthouse['categories'] ?? []) as $key => $category) {
            $scores[str_replace('-', '_', $key)] = isset($category['score'])
                ? (int) round($category['score'] * 100)
                : null;

            foreach (($category['auditRefs'] ?? []) as $reference) {
                if (! empty($reference['id'])) {
                    $auditCategories[$reference['id']] = $key;
                }
            }
        }

        $metricIds = [
            'first-contentful-paint' => 'First Contentful Paint',
            'largest-contentful-paint' => 'Largest Contentful Paint',
            'speed-index' => 'Speed Index',
            'total-blocking-time' => 'Total Blocking Time',
            'cumulative-layout-shift' => 'Cumulative Layout Shift',
            'interactive' => 'Time to Interactive',
            'server-response-time' => 'Server response time',
            'total-byte-weight' => 'Total page weight',
        ];

        $metrics = [];
        foreach ($metricIds as $id => $label) {
            $audit = $lighthouse['audits'][$id] ?? null;
            if (is_array($audit)) {
                $metrics[$id] = [
                    'label' => $label,
                    'display' => $audit['displayValue'] ?? null,
                    'numeric_value' => $audit['numericValue'] ?? null,
                    'unit' => $audit['numericUnit'] ?? null,
                    'score' => isset($audit['score']) ? (int) round($audit['score'] * 100) : null,
                ];
            }
        }

        $findings = [];
        foreach (($lighthouse['audits'] ?? []) as $id => $audit) {
            if (! is_array($audit) || ! isset($audit['score']) || $audit['score'] === null || $audit['score'] >= .9) {
                continue;
            }

            if (in_array($audit['scoreDisplayMode'] ?? '', ['notApplicable', 'manual', 'informative'], true)) {
                continue;
            }

            $category = $this->mapCategory($auditCategories[$id] ?? 'best-practices', $id);
            $score = (float) $audit['score'];
            $severity = $score <= .25 ? 'high' : ($score <= .65 ? 'medium' : 'low');
            $display = $audit['displayValue'] ?? null;
            $explanation = $audit['explanation'] ?? null;

            $findings[] = [
                'category' => $category,
                'rule_key' => "lighthouse-{$strategy}-{$id}",
                'severity' => $severity,
                'title' => ($audit['title'] ?? str_replace('-', ' ', ucfirst($id)))." ({$strategy})",
                'description' => $this->plainText($audit['description'] ?? 'This Lighthouse audit did not meet the recommended threshold.'),
                'evidence' => trim(implode(' ', array_filter([$display, $explanation]))),
                'recommendation' => 'Review the affected resources and implement the Lighthouse guidance, then retest this page on '.$strategy.'.',
                'impact' => $severity === 'high' ? 'high' : 'medium',
                'effort' => $this->estimateEffort($id),
                'source' => 'Google Lighthouse',
                'details' => [
                    'strategy' => $strategy,
                    'audit_id' => $id,
                    'score' => (int) round($score * 100),
                ],
            ];

            if (count($findings) >= 55) {
                break;
            }
        }

        return [
            'strategy' => $strategy,
            'scores' => $scores,
            'metrics' => $metrics,
            'findings' => $findings,
            'screenshot' => $lighthouse['audits']['final-screenshot']['details']['data'] ?? null,
            'version' => $lighthouse['lighthouseVersion'] ?? null,
            'fetch_time' => $lighthouse['fetchTime'] ?? null,
            'final_url' => $lighthouse['finalUrl'] ?? null,
            'warnings' => $lighthouse['runWarnings'] ?? [],
        ];
    }

    private function mapCategory(string $category, string $auditId): string
    {
        if (preg_match('/javascript|css|mainthread|bootup|third-party|dom-size|byte-weight|resource|cache|compression/', $auditId)) {
            return 'code';
        }

        return match ($category) {
            'performance' => 'performance',
            'accessibility' => 'accessibility',
            'seo' => 'seo',
            default => 'technical',
        };
    }

    private function estimateEffort(string $auditId): string
    {
        return preg_match('/image|cache|compression|font-display|redirect/', $auditId) ? 'small' : 'medium';
    }

    private function plainText(string $text): string
    {
        $text = preg_replace('/\\[([^]]+)]\\([^)]+\\)/', '$1', $text) ?? $text;
        $text = str_replace(['`', "\n"], ['', ' '], $text);

        return trim(preg_replace('/\\s+/', ' ', $text) ?? $text);
    }
}
