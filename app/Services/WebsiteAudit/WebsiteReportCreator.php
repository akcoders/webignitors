<?php

namespace App\Services\WebsiteAudit;

use App\Exceptions\DailyReportLimitExceeded;
use App\Jobs\ProcessWebsiteReport;
use App\Models\User;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebsiteReportCreator
{
    public function __construct(private readonly SafeWebsiteUrl $safeUrl) {}

    public function create(User $user, string $url): WebsiteReport
    {
        $normalized = $this->safeUrl->normalize($url);
        $host = strtolower((string) parse_url($normalized, PHP_URL_HOST));

        return DB::transaction(function () use ($user, $normalized, $host): WebsiteReport {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $latestReport = $lockedUser->websiteReports()->latest()->first();

            if ($latestReport && $latestReport->created_at->isAfter(now()->subDay())) {
                throw new DailyReportLimitExceeded($latestReport->created_at->addDay());
            }

            $report = WebsiteReport::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $lockedUser->id,
                'requested_url' => $normalized,
                'domain' => $host,
                'status' => 'queued',
                'current_stage' => 'Waiting for the audit worker',
                'progress' => 2,
                'page_limit' => config('audit.page_limit'),
            ]);

            ProcessWebsiteReport::dispatch($report)->afterCommit();

            return $report;
        });
    }
}
