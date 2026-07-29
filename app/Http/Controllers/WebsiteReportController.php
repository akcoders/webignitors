<?php

namespace App\Http\Controllers;

use App\Models\WebsiteReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WebsiteReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = $request->user()
            ->websiteReports()
            ->latest()
            ->paginate(12);

        return view('dashboard.index', compact('reports'));
    }

    public function show(Request $request, WebsiteReport $report): View
    {
        $this->ensureOwner($request, $report);

        $report->load([
            'pages',
            'findings' => fn ($query) => $query->orderBy('position'),
            'apiRuns' => fn ($query) => $query->latest(),
        ]);

        return view('reports.show', [
            'report' => $report,
            'page' => $report->pages->first(),
            'findingsByCategory' => $report->findings->groupBy('category'),
        ]);
    }

    public function status(Request $request, WebsiteReport $report): JsonResponse
    {
        $this->ensureOwner($request, $report);

        return response()->json([
            'status' => $report->status,
            'stage' => $report->current_stage,
            'progress' => $report->progress,
            'completed' => $report->status === 'completed',
            'failed' => $report->status === 'failed',
            'updated_at' => $report->updated_at?->toIso8601String(),
        ]);
    }

    public function download(Request $request, WebsiteReport $report): StreamedResponse|RedirectResponse
    {
        $this->ensureOwner($request, $report);

        if (! $request->user()->hasVerifiedEmail()) {
            return to_route('verification.notice')->with(
                'status',
                'Verify your email address before downloading private reports.'
            );
        }

        if ($report->status !== 'completed' || ! $report->pdf_path || ! Storage::disk('local')->exists($report->pdf_path)) {
            return back()->withErrors(['report' => 'The PDF is not ready yet.']);
        }

        return Storage::disk('local')->download(
            $report->pdf_path,
            'webignitors-'.$report->domain.'-website-report.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function screenshot(Request $request, WebsiteReport $report, string $device): StreamedResponse
    {
        $this->ensureOwner($request, $report);
        abort_unless(in_array($device, ['mobile', 'desktop'], true), 404);

        $page = $report->pages()->firstOrFail();
        $path = $device === 'mobile' ? $page->mobile_screenshot_path : $page->desktop_screenshot_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    private function ensureOwner(Request $request, WebsiteReport $report): void
    {
        abort_unless($report->user_id === $request->user()->id, 403);
    }
}
