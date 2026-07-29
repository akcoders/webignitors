<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteAuditApiRun;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class W3cValidatorClient
{
    /**
     * @return array{score: int, errors: int, warnings: int, findings: array<int, array<string, mixed>>}
     */
    public function audit(WebsiteReport $report, string $url): array
    {
        $started = microtime(true);
        $run = WebsiteAuditApiRun::create([
            'website_report_id' => $report->id,
            'provider' => 'W3C HTML Checker',
            'operation' => 'html-validation',
            'status' => 'running',
        ]);

        try {
            $response = Http::acceptJson()
                ->withHeaders(['User-Agent' => config('audit.user_agent')])
                ->timeout(45)
                ->retry(1, 1000, throw: false)
                ->get(config('audit.w3c.endpoint'), ['doc' => $url, 'out' => 'json']);

            if (! $response->successful()) {
                throw new RuntimeException("W3C Validator returned HTTP {$response->status()}.");
            }

            $messages = $response->json('messages', []);
            $errors = 0;
            $warnings = 0;
            $findings = [];

            foreach ($messages as $index => $message) {
                $type = $message['type'] ?? 'info';
                $isError = $type === 'error';
                $errors += $isError ? 1 : 0;
                $warnings += $isError ? 0 : 1;

                if ($index >= 30) {
                    continue;
                }

                $findings[] = [
                    'category' => 'html',
                    'rule_key' => 'w3c-'.sha1(($message['message'] ?? '').($message['lastLine'] ?? $index)),
                    'severity' => $isError ? 'medium' : 'low',
                    'title' => $isError ? 'HTML validation error' : 'HTML validation warning',
                    'description' => trim($message['message'] ?? 'The W3C checker identified a markup issue.'),
                    'evidence' => isset($message['lastLine']) ? 'Reported near line '.$message['lastLine'].'.' : null,
                    'recommendation' => 'Correct the markup and validate the page again to improve browser consistency and maintainability.',
                    'impact' => $isError ? 'medium' : 'low',
                    'effort' => 'small',
                    'source' => 'W3C HTML Checker',
                    'details' => $message,
                ];
            }

            $result = [
                'score' => max(0, 100 - ($errors * 5) - min(20, $warnings)),
                'errors' => $errors,
                'warnings' => $warnings,
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
