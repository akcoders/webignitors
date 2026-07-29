<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteAuditApiRun;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MdnObservatoryClient
{
    /**
     * @return array<string, mixed>
     */
    public function audit(WebsiteReport $report, string $host): array
    {
        $started = microtime(true);
        $run = WebsiteAuditApiRun::create([
            'website_report_id' => $report->id,
            'provider' => 'MDN HTTP Observatory',
            'operation' => 'security-headers',
            'status' => 'running',
        ]);

        try {
            $response = Http::acceptJson()
                ->withHeaders(['User-Agent' => config('audit.user_agent')])
                ->timeout(45)
                ->retry(1, 1200, throw: false)
                ->post(config('audit.observatory.endpoint').'?'.http_build_query(['host' => $host]));

            if (! $response->successful()) {
                throw new RuntimeException("MDN Observatory returned HTTP {$response->status()}.");
            }

            $payload = $response->json();
            $score = max(0, min(100, (int) ($payload['score'] ?? 0)));
            $grade = $payload['grade'] ?? 'Not available';
            $findings = [];

            if ($score < 80) {
                $findings[] = [
                    'category' => 'security',
                    'rule_key' => 'mdn-observatory-grade',
                    'severity' => $score < 50 ? 'high' : 'medium',
                    'title' => "Security-header grade: {$grade}",
                    'description' => 'The MDN HTTP Observatory identified missing or incomplete defensive browser headers.',
                    'evidence' => "Score {$score}/100; ".($payload['tests_failed'] ?? 0).' checks failed.',
                    'recommendation' => 'Review Content-Security-Policy, HSTS, framing, referrer, content-type and cross-origin header configuration.',
                    'impact' => 'high',
                    'effort' => 'medium',
                    'source' => 'MDN HTTP Observatory',
                    'details' => $payload,
                ];
            }

            $result = [
                'score' => $score,
                'grade' => $grade,
                'tests_passed' => $payload['tests_passed'] ?? null,
                'tests_failed' => $payload['tests_failed'] ?? null,
                'details_url' => $payload['details_url'] ?? null,
                'findings' => $findings,
            ];

            $run->update([
                'status' => 'completed',
                'http_status' => $response->status(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'response_summary' => array_diff_key($result, ['findings' => true]),
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
}
