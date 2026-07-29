<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteAuditApiRun;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CruxClient
{
    /**
     * @return array<string, mixed>|null
     */
    public function audit(WebsiteReport $report, string $url): ?array
    {
        $key = config('audit.crux.api_key');
        if (! config('audit.crux.enabled') || ! $key) {
            return null;
        }

        $started = microtime(true);
        $run = WebsiteAuditApiRun::create([
            'website_report_id' => $report->id,
            'provider' => 'Chrome UX Report',
            'operation' => 'field-performance',
            'status' => 'running',
        ]);

        try {
            $response = Http::acceptJson()
                ->timeout(30)
                ->post(config('audit.crux.endpoint').'?key='.rawurlencode($key), [
                    'url' => $url,
                    'metrics' => [
                        'largest_contentful_paint',
                        'interaction_to_next_paint',
                        'cumulative_layout_shift',
                        'first_contentful_paint',
                        'experimental_time_to_first_byte',
                    ],
                ]);

            if ($response->status() === 404) {
                $run->update([
                    'status' => 'completed',
                    'http_status' => 404,
                    'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                    'response_summary' => ['available' => false],
                ]);

                return ['available' => false, 'metrics' => []];
            }

            if (! $response->successful()) {
                throw new RuntimeException("CrUX returned HTTP {$response->status()}.");
            }

            $record = $response->json('record', []);
            $metrics = [];
            foreach (($record['metrics'] ?? []) as $keyName => $metric) {
                $metrics[$keyName] = [
                    'p75' => $metric['percentiles']['p75'] ?? null,
                    'histogram' => $metric['histogram'] ?? null,
                ];
            }

            $result = [
                'available' => true,
                'metrics' => $metrics,
                'collection_period' => $record['collectionPeriod'] ?? null,
            ];

            $run->update([
                'status' => 'completed',
                'http_status' => $response->status(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'response_summary' => [
                    'available' => true,
                    'metric_count' => count($metrics),
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
}
