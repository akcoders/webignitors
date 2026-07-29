<?php

namespace App\Services\WebsiteAudit;

use App\Models\WebsiteReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfReportGenerator
{
    public function generate(WebsiteReport $report): string
    {
        $report->loadMissing(['user', 'pages', 'findings.page', 'apiRuns']);
        $page = $report->pages->first();
        $screenshots = [
            'mobile' => $this->dataUri($page?->mobile_screenshot_path),
            'desktop' => $this->dataUri($page?->desktop_screenshot_path),
        ];

        $pdf = Pdf::loadView('reports.pdf', [
            'report' => $report,
            'page' => $page,
            'screenshots' => $screenshots,
            'findingsByCategory' => $report->findings->groupBy('category'),
        ])->setPaper('a4');

        $path = "reports/{$report->uuid}/webignitors-website-report.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    private function dataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = $extension === 'png' ? 'image/png' : 'image/jpeg';

        return "data:{$mime};base64,".base64_encode(Storage::disk('local')->get($path));
    }
}
