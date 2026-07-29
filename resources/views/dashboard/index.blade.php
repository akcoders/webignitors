@extends('layouts.app')

@section('title', 'Website Report Dashboard')
@section('meta_description', 'Your private WebIgnitors website intelligence reports.')

@section('content')
<header class="dashboard-hero">
    <div class="container">
        <div class="dashboard-topline">
            <div>
                <span class="dashboard-eyebrow">Private intelligence dashboard</span>
                <h1>Hello, {{ str(auth()->user()->name)->before(' ') }}.</h1>
                <p>Audit a website, follow its progress and turn the findings into a focused improvement plan.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dashboard-logout" type="submit"><i class="bi bi-box-arrow-right"></i> Sign out</button>
            </form>
        </div>

        <form class="dashboard-audit-form" method="POST" action="{{ route('audit.store') }}">
            @csrf
            <div>
                <label for="dashboard-url">Run another report</label>
                <input id="dashboard-url" name="url" placeholder="https://yourwebsite.com" required>
            </div>
            <button type="submit">Start audit <i class="bi bi-arrow-up-right"></i></button>
        </form>
        @error('url')<div class="audit-form-error mt-2">{{ $message }}</div>@enderror
    </div>
</header>

<section class="dashboard-reports section-space-sm">
    <div class="container">
        <div class="dashboard-section-head">
            <div>
                <span class="section-label">Saved reports</span>
                <h2>Your website intelligence.</h2>
            </div>
            @if (! auth()->user()->hasVerifiedEmail())
                <a class="verify-pill" href="{{ route('verification.notice') }}"><i class="bi bi-envelope-exclamation"></i> Verify email to download PDFs</a>
            @endif
        </div>

        @if ($reports->isEmpty())
            <div class="dashboard-empty">
                <i class="bi bi-radar"></i>
                <h3>No reports yet.</h3>
                <p>Enter a public website URL above to create your first detailed report.</p>
            </div>
        @else
            <div class="report-card-grid">
                @foreach ($reports as $report)
                    <a class="report-card" href="{{ route('reports.show', $report) }}">
                        <div class="report-card-top">
                            <span class="report-status status-{{ $report->status }}"><i></i>{{ ucfirst($report->status) }}</span>
                            <span>{{ $report->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="report-domain-icon">{{ strtoupper(substr($report->domain, 0, 2)) }}</div>
                        <h3>{{ $report->domain }}</h3>
                        <p>{{ $report->website_title ?: $report->current_stage }}</p>
                        @if ($report->status === 'completed')
                            <div class="report-card-score">
                                <strong>{{ data_get($report->scores, 'overall', '—') }}</strong>
                                <span>Overall score</span>
                                <i><b style="width: {{ data_get($report->scores, 'overall', 0) }}%"></b></i>
                            </div>
                        @else
                            <div class="report-card-progress">
                                <span>{{ $report->current_stage }}</span>
                                <i><b style="width: {{ $report->progress }}%"></b></i>
                            </div>
                        @endif
                        <span class="report-card-open">Open report <i class="bi bi-arrow-up-right"></i></span>
                    </a>
                @endforeach
            </div>
            <div class="mt-5">{{ $reports->links() }}</div>
        @endif
    </div>
</section>
@endsection
