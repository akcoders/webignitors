<?php

namespace App\Jobs;

use App\Models\WebsiteReport;
use App\Services\WebsiteAudit\WebsiteAuditRunner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessWebsiteReport implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 330;

    public function __construct(public WebsiteReport $report) {}

    public function handle(WebsiteAuditRunner $runner): void
    {
        $runner->run($this->report->fresh());
    }

    public function failed(?\Throwable $exception): void
    {
        $this->report->fresh()?->update([
            'status' => 'failed',
            'current_stage' => 'The audit could not be completed',
            'error_message' => $exception?->getMessage() ?? 'An unexpected audit error occurred.',
            'completed_at' => now(),
        ]);
    }
}
