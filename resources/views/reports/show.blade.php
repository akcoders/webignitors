@extends('layouts.app')

@section('title', ($report->website_title ?: $report->domain).' Website Report')
@section('meta_description', 'Private WebIgnitors website intelligence report for '.$report->domain.'.')

@php
    $categoryLabels = [
        'performance' => ['Performance', 'bi-speedometer2'],
        'design' => ['Design & layout', 'bi-bezier2'],
        'code' => ['JavaScript & CSS', 'bi-braces'],
        'accessibility' => ['Accessibility', 'bi-universal-access'],
        'seo' => ['Technical SEO', 'bi-search'],
        'marketing' => ['Marketing & conversion', 'bi-bullseye'],
        'security' => ['Security & trust', 'bi-shield-lock'],
        'technical' => ['Technical quality', 'bi-gear'],
        'html' => ['HTML quality', 'bi-code-square'],
    ];
    $scoreOrder = ['overall', 'performance', 'design', 'code', 'accessibility', 'seo', 'marketing', 'security', 'technical'];
@endphp

@section('content')
<header class="report-hero">
    <div class="report-hero-grid" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="report-breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="bi bi-arrow-left"></i> All reports</a>
            <span>Report {{ substr($report->uuid, 0, 8) }}</span>
        </div>
        <div class="row g-5 align-items-end">
            <div class="col-lg-8">
                <span class="report-status status-{{ $report->status }}"><i></i>{{ ucfirst($report->status) }}</span>
                <h1>{{ $report->domain }}</h1>
                <p>{{ $report->final_url ?: $report->requested_url }}</p>
            </div>
            <div class="col-lg-4 report-hero-actions">
                @if ($report->status === 'completed')
                    @if (auth()->user()->hasVerifiedEmail())
                        <a class="btn btn-lime" href="{{ route('reports.download', $report) }}">
                            Download branded PDF <i class="bi bi-download"></i>
                        </a>
                    @else
                        <a class="btn btn-lime" href="{{ route('verification.notice') }}">
                            Verify email to download <i class="bi bi-envelope-check"></i>
                        </a>
                    @endif
                @endif
                <span>Created {{ $report->created_at->format('d M Y, g:i A') }}</span>
            </div>
        </div>
    </div>
</header>

@if (in_array($report->status, ['queued', 'processing']))
    <section class="report-processing" data-report-status-url="{{ route('reports.status', $report) }}">
        <div class="container">
            <div class="processing-card">
                <div class="processing-visual" aria-hidden="true">
                    <div class="processing-orbit orbit-one"></div>
                    <div class="processing-orbit orbit-two"></div>
                    <div class="processing-core"><i class="bi bi-radar"></i></div>
                </div>
                <div>
                    <span class="section-label">Analysis in progress</span>
                    <h2 data-report-stage>{{ $report->current_stage }}</h2>
                    <p>Multiple independent checks are being collected and translated into a prioritised, plain-language report. You can leave this page and return later.</p>
                    <div class="processing-progress">
                        <div><span>Report progress</span><strong data-report-percent>{{ $report->progress }}%</strong></div>
                        <i><b data-report-progress style="width: {{ $report->progress }}%"></b></i>
                    </div>
                    <div class="processing-sources">
                        <span><i class="bi bi-check2-circle"></i> MySQL record created</span>
                        <span><i class="bi bi-lightning"></i> API jobs queued</span>
                        <span><i class="bi bi-lock"></i> Private to your account</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@elseif ($report->status === 'failed')
    <section class="report-processing">
        <div class="container">
            <div class="processing-card processing-failed">
                <div class="processing-visual"><div class="processing-core"><i class="bi bi-exclamation-triangle"></i></div></div>
                <div>
                    <span class="section-label">Audit interrupted</span>
                    <h2>We could not complete this report.</h2>
                    <p>{{ $report->error_message ?: 'The website or an audit service did not respond as expected.' }}</p>
                    <a class="btn btn-ink" href="{{ route('audit.create') }}">Try another URL</a>
                </div>
            </div>
        </div>
    </section>
@else
    <section class="report-overview">
        <div class="container">
            <div class="report-score-grid">
                @foreach ($scoreOrder as $key)
                    @php
                        $score = data_get($report->scores, $key);
                        $label = $key === 'overall' ? 'Overall' : ($categoryLabels[$key][0] ?? ucfirst($key));
                    @endphp
                    @if ($score !== null)
                        <article class="report-score-card {{ $key === 'overall' ? 'score-overall' : '' }}">
                            <span>{{ $label }}</span>
                            <strong>{{ $score }}</strong>
                            <div><i style="width: {{ $score }}%"></i></div>
                            <small>
                                @if ($score >= 90) Excellent
                                @elseif ($score >= 78) Strong
                                @elseif ($score >= 65) Promising
                                @elseif ($score >= 50) Needs work
                                @else Priority
                                @endif
                            </small>
                        </article>
                    @endif
                @endforeach
            </div>

            <div class="report-summary-grid">
                <article class="executive-summary">
                    <span class="section-label">Executive summary</span>
                    <h2>{{ data_get($report->summary, 'rating', 'Website audit complete') }}</h2>
                    <p>{{ data_get($report->summary, 'narrative') }}</p>
                    <div class="summary-strengths">
                        <span>Strongest signals</span>
                        @foreach (data_get($report->summary, 'strengths', []) as $strength)
                            <strong>{{ $strength }}</strong>
                        @endforeach
                    </div>
                </article>
                <article class="severity-summary">
                    <span class="section-label">Finding profile</span>
                    @foreach (['critical', 'high', 'medium', 'low'] as $severity)
                        <div>
                            <span><i class="severity-dot severity-{{ $severity }}"></i>{{ ucfirst($severity) }}</span>
                            <strong>{{ data_get($report->summary, "severity_counts.{$severity}", 0) }}</strong>
                        </div>
                    @endforeach
                    <p>{{ data_get($report->summary, 'finding_count', $report->findings->count()) }} evidence-led observations across the audited page.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="report-priorities">
        <div class="container">
            <div class="report-section-heading">
                <div>
                    <span class="section-label">Priority roadmap</span>
                    <h2>Start where impact meets momentum.</h2>
                </div>
                <p>Ordered by severity, business impact and likely implementation effort.</p>
            </div>
            <div class="priority-list">
                @foreach (array_slice($report->top_recommendations ?? [], 0, 8) as $index => $priority)
                    <article>
                        <span class="priority-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <div class="priority-tags">
                                <span class="severity-{{ $priority['severity'] }}">{{ strtoupper($priority['severity']) }}</span>
                                <span>{{ ucfirst($priority['category']) }}</span>
                            </div>
                            <h3>{{ $priority['title'] }}</h3>
                            <p>{{ $priority['recommendation'] }}</p>
                        </div>
                        <div class="priority-effort">
                            <span>Impact <strong>{{ ucfirst($priority['impact']) }}</strong></span>
                            <span>Effort <strong>{{ ucfirst($priority['effort']) }}</strong></span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($page)
        <section class="report-evidence section-space-sm">
            <div class="container">
                <div class="report-section-heading">
                    <div>
                        <span class="section-label">Measured evidence</span>
                        <h2>Performance and page structure.</h2>
                    </div>
                    <p>Lab values may vary between runs. Use them as directional evidence alongside real-user field data when available.</p>
                </div>
                <div class="row g-4">
                    @foreach (['mobile' => 'Mobile lab test', 'desktop' => 'Desktop lab test'] as $device => $label)
                        <div class="col-lg-6">
                            <article class="metric-panel">
                                <div class="metric-panel-head"><h3>{{ $label }}</h3><i class="bi bi-{{ $device === 'mobile' ? 'phone' : 'display' }}"></i></div>
                                @forelse (data_get($page->metrics, $device, []) as $metric)
                                    <div class="metric-row-detail">
                                        <span>{{ $metric['label'] }}</span>
                                        <strong>{{ $metric['display'] ?? '—' }}</strong>
                                    </div>
                                @empty
                                    <p class="text-soft">This data source was unavailable during the audit.</p>
                                @endforelse
                            </article>
                        </div>
                    @endforeach
                    <div class="col-lg-6">
                        <article class="metric-panel">
                            <div class="metric-panel-head"><h3>Page structure</h3><i class="bi bi-diagram-3"></i></div>
                            @foreach ([
                                'DOM elements' => data_get($page->meta, 'dom_nodes'),
                                'Images' => data_get($page->meta, 'images'),
                                'Scripts' => data_get($page->meta, 'scripts'),
                                'Stylesheets' => data_get($page->meta, 'stylesheets'),
                                'Internal links' => data_get($page->meta, 'internal_links'),
                                'Words analysed' => data_get($page->meta, 'word_count'),
                            ] as $label => $value)
                                <div class="metric-row-detail"><span>{{ $label }}</span><strong>{{ is_numeric($value) ? number_format($value) : '—' }}</strong></div>
                            @endforeach
                        </article>
                    </div>
                    <div class="col-lg-6">
                        <article class="metric-panel">
                            <div class="metric-panel-head"><h3>External validation</h3><i class="bi bi-patch-check"></i></div>
                            <div class="metric-row-detail"><span>HTML errors</span><strong>{{ data_get($page->metrics, 'html_validation.errors', '—') }}</strong></div>
                            <div class="metric-row-detail"><span>HTML warnings</span><strong>{{ data_get($page->metrics, 'html_validation.warnings', '—') }}</strong></div>
                            <div class="metric-row-detail"><span>Security grade</span><strong>{{ data_get($page->metrics, 'security.grade', '—') }}</strong></div>
                            <div class="metric-row-detail"><span>Real-user data</span><strong>{{ data_get($page->metrics, 'field_data.available') ? 'Available' : 'Not available' }}</strong></div>
                        </article>
                    </div>
                </div>

                @if ($page->mobile_screenshot_path || $page->desktop_screenshot_path)
                    <div class="report-screenshots">
                        @if ($page->desktop_screenshot_path)
                            <figure>
                                <figcaption>Desktop evidence</figcaption>
                                <img src="{{ route('reports.screenshot', [$report, 'desktop']) }}" alt="Desktop screenshot of {{ $report->domain }}">
                            </figure>
                        @endif
                        @if ($page->mobile_screenshot_path)
                            <figure class="mobile-shot">
                                <figcaption>Mobile evidence</figcaption>
                                <img src="{{ route('reports.screenshot', [$report, 'mobile']) }}" alt="Mobile screenshot of {{ $report->domain }}">
                            </figure>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="report-findings section-space-sm">
        <div class="container">
            <div class="report-section-heading">
                <div>
                    <span class="section-label">Detailed findings</span>
                    <h2>Evidence, meaning and next move.</h2>
                </div>
                <p>Automated findings are decision support, not a substitute for manual accessibility, content or security review.</p>
            </div>

            <div class="finding-category-list">
                @foreach ($findingsByCategory as $category => $findings)
                    @php [$categoryLabel, $categoryIcon] = $categoryLabels[$category] ?? [ucfirst($category), 'bi-check2-square']; @endphp
                    <section class="finding-category">
                        <div class="finding-category-head">
                            <span><i class="bi {{ $categoryIcon }}"></i></span>
                            <div><h3>{{ $categoryLabel }}</h3><p>{{ $findings->count() }} findings</p></div>
                            <strong>{{ data_get($report->scores, $category, '—') }}</strong>
                        </div>
                        <div class="accordion finding-accordion" id="finding-{{ $category }}">
                            @foreach ($findings as $finding)
                                <div class="accordion-item">
                                    <h4 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#finding-{{ $finding->id }}">
                                            <span class="severity-dot severity-{{ $finding->severity }}"></span>
                                            <span>{{ $finding->title }}</span>
                                            <small>{{ strtoupper($finding->severity) }}</small>
                                        </button>
                                    </h4>
                                    <div id="finding-{{ $finding->id }}" class="accordion-collapse collapse" data-bs-parent="#finding-{{ $category }}">
                                        <div class="accordion-body">
                                            <div class="finding-explanation">
                                                <div><span>What it means</span><p>{{ $finding->description }}</p></div>
                                                <div><span>Evidence</span><p>{{ $finding->evidence ?: 'The automated check did not provide additional element-level evidence.' }}</p></div>
                                                <div class="finding-fix"><span>Recommended move</span><p>{{ $finding->recommendation }}</p></div>
                                            </div>
                                            <div class="finding-meta">
                                                <span>Impact <strong>{{ ucfirst($finding->impact) }}</strong></span>
                                                <span>Effort <strong>{{ ucfirst($finding->effort) }}</strong></span>
                                                <span>Source <strong>{{ $finding->source }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>

    <section class="report-download-panel">
        <div class="container">
            <div class="report-download-inner">
                <div>
                    <span>WEBIGNITORS / WEBSITE INTELLIGENCE</span>
                    <h2>Take the complete roadmap with you.</h2>
                    <p>The private PDF includes the executive summary, scores, evidence, priorities, detailed findings and methodology.</p>
                </div>
                @if (auth()->user()->hasVerifiedEmail())
                    <a class="btn btn-lime" href="{{ route('reports.download', $report) }}">Download PDF <i class="bi bi-download"></i></a>
                @else
                    <a class="btn btn-lime" href="{{ route('verification.notice') }}">Verify email to unlock <i class="bi bi-envelope-check"></i></a>
                @endif
            </div>
        </div>
    </section>
@endif
@endsection
