<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteAuditApiRun;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BrowserlessClient
{
    public function isConfigured(): bool
    {
        return (bool) config('audit.browserless.enabled') && (bool) config('audit.browserless.token');
    }

    public function screenshot(WebsiteReport $report, string $url, string $device): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $started = microtime(true);
        $run = WebsiteAuditApiRun::create([
            'website_report_id' => $report->id,
            'provider' => 'Browserless',
            'operation' => "screenshot-{$device}",
            'status' => 'running',
        ]);

        try {
            $viewport = $device === 'mobile'
                ? ['width' => 390, 'height' => 844, 'deviceScaleFactor' => 1]
                : ['width' => 1440, 'height' => 1000, 'deviceScaleFactor' => 1];

            $response = Http::timeout(90)
                ->post(config('audit.browserless.base_url').'/screenshot?token='.rawurlencode(config('audit.browserless.token')), [
                    'url' => $url,
                    'viewport' => $viewport,
                    'gotoOptions' => ['waitUntil' => 'networkidle2', 'timeout' => 60000],
                    'options' => ['type' => 'jpeg', 'quality' => 78, 'fullPage' => true],
                ]);

            if (! $response->successful()) {
                throw new RuntimeException("Browserless returned HTTP {$response->status()}.");
            }

            $run->update([
                'status' => 'completed',
                'http_status' => $response->status(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'response_summary' => ['bytes' => strlen($response->body())],
            ]);

            return $response->body();
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
