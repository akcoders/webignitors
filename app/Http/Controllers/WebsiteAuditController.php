<?php

namespace App\Http\Controllers;

use App\Exceptions\DailyReportLimitExceeded;
use App\Http\Requests\StoreWebsiteReportRequest;
use App\Services\WebsiteAudit\SafeWebsiteUrl;
use App\Services\WebsiteAudit\WebsiteReportCreator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebsiteAuditController extends Controller
{
    public function create(): View
    {
        return view('pages.website-audit');
    }

    public function store(
        StoreWebsiteReportRequest $request,
        SafeWebsiteUrl $safeUrl,
        WebsiteReportCreator $creator
    ): RedirectResponse {
        $url = $safeUrl->normalize($request->validated('url'));

        if (! $request->user()) {
            $request->session()->put('pending_audit_url', $url);

            return to_route('register')->with(
                'status',
                'Create your free account to run the complete audit and save the report.'
            );
        }

        try {
            $report = $creator->create($request->user(), $url);
        } catch (DailyReportLimitExceeded $exception) {
            return back()->withErrors([
                'url' => $exception->getMessage(),
            ])->withInput();
        }

        return to_route('reports.show', $report)
            ->with('success', 'Your audit has started. This page updates automatically.');
    }
}
