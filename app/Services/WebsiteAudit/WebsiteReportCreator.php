<?php

namespace App\Services\WebsiteAudit;

use App\Jobs\ProcessWebsiteReport;
use App\Models\User;
use App\Models\WebsiteReport;
use Illuminate\Support\Str;

class WebsiteReportCreator
{
    public function __construct(private readonly SafeWebsiteUrl $safeUrl) {}

    public function create(User $user, string $url): WebsiteReport
    {
        $normalized = $this->safeUrl->normalize($url);
        $host = strtolower((string) parse_url($normalized, PHP_URL_HOST));

        $report = WebsiteReport::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'requested_url' => $normalized,
            'domain' => $host,
            'status' => 'queued',
            'current_stage' => 'Waiting for the audit worker',
            'progress' => 2,
            'page_limit' => config('audit.page_limit'),
        ]);

        ProcessWebsiteReport::dispatch($report)->afterCommit();

        return $report;
    }
}
