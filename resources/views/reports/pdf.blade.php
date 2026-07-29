<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebIgnitors Website Intelligence Report — {{ $report->domain }}</title>
    <style>
        @page { margin: 19mm 16mm 18mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111715; font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; line-height: 1.5; }
        h1, h2, h3, h4, p { margin-top: 0; }
        h1, h2, h3, h4 { line-height: 1.1; }
        .cover { position: relative; height: 257mm; margin: -19mm -16mm -18mm; padding: 24mm 19mm; color: #fffef9; background: #111715; page-break-after: always; }
        .cover-accent { width: 88mm; height: 10mm; margin-bottom: 37mm; background: #d9ff57; }
        .brand { font-size: 17pt; font-weight: bold; }
        .brand-mark { display: inline-block; width: 12mm; height: 12mm; margin-right: 4mm; border-radius: 50%; color: #111715; background: #d9ff57; font-size: 8pt; line-height: 12mm; text-align: center; vertical-align: middle; }
        .cover-kicker, .eyebrow { color: #ff6b4a; font-size: 8pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; }
        .cover h1 { width: 155mm; margin: 11mm 0 8mm; font-size: 38pt; letter-spacing: -2px; }
        .cover h1 span { color: #d9ff57; }
        .cover-url { color: rgba(255,255,255,.62); font-size: 13pt; }
        .cover-score { position: absolute; right: 19mm; bottom: 31mm; width: 46mm; height: 46mm; border: 4mm solid #745cff; border-radius: 50%; text-align: center; }
        .cover-score strong { display: block; margin-top: 9mm; font-size: 24pt; line-height: 1; }
        .cover-score span { color: rgba(255,255,255,.58); font-size: 7pt; text-transform: uppercase; }
        .cover-meta { position: absolute; bottom: 24mm; left: 19mm; color: rgba(255,255,255,.58); font-size: 8pt; }
        .page-title { margin-bottom: 8mm; padding-bottom: 4mm; border-bottom: 1px solid #d8d8d2; }
        .page-title h2 { margin: 2mm 0; font-size: 23pt; letter-spacing: -1px; }
        .page-title p { max-width: 150mm; color: #626964; }
        .score-table { width: 100%; margin: 7mm 0 10mm; border-collapse: collapse; }
        .score-table td { width: 25%; padding: 5mm 4mm; border: 1px solid #d8d8d2; vertical-align: top; }
        .score-table strong { display: block; font-size: 22pt; }
        .score-table span { color: #626964; font-size: 7.5pt; text-transform: uppercase; }
        .summary { margin: 7mm 0; padding: 7mm; border-left: 4mm solid #d9ff57; background: #f3f1e9; }
        .summary h3 { margin-bottom: 3mm; font-size: 16pt; }
        .priority { width: 100%; margin-bottom: 3.5mm; padding: 4.5mm; border: 1px solid #d8d8d2; page-break-inside: avoid; }
        .priority-number { width: 12mm; color: #745cff; font-size: 14pt; font-weight: bold; vertical-align: top; }
        .priority h3 { margin: 0 0 1.5mm; font-size: 11pt; }
        .priority p { margin: 0; color: #626964; }
        .tag { display: inline-block; margin: 0 1mm 1.5mm 0; padding: 1mm 2mm; border-radius: 3mm; background: #ece9df; font-size: 6.5pt; font-weight: bold; text-transform: uppercase; }
        .tag-critical, .tag-high { color: #fff; background: #c53b28; }
        .tag-medium { background: #ffd85c; }
        .metric-table { width: 100%; margin: 5mm 0 8mm; border-collapse: collapse; }
        .metric-table th, .metric-table td { padding: 3mm; border-bottom: 1px solid #d8d8d2; text-align: left; }
        .metric-table th { color: #626964; font-size: 7pt; text-transform: uppercase; }
        .screenshot { max-width: 100%; max-height: 145mm; margin: 4mm 0 9mm; border: 1px solid #d8d8d2; }
        .category { margin-bottom: 8mm; page-break-before: always; }
        .category-heading { margin-bottom: 5mm; padding: 5mm; color: #fff; background: #111715; }
        .category-heading h2 { display: inline-block; margin: 0; font-size: 18pt; }
        .category-heading strong { float: right; color: #d9ff57; font-size: 18pt; }
        .finding { margin-bottom: 5mm; padding-bottom: 5mm; border-bottom: 1px solid #d8d8d2; page-break-inside: avoid; }
        .finding h3 { margin: 1.5mm 0 2mm; font-size: 12pt; }
        .finding-grid { width: 100%; border-collapse: collapse; }
        .finding-grid td { width: 50%; padding: 2.5mm 4mm 2.5mm 0; vertical-align: top; }
        .finding-label { display: block; margin-bottom: 1mm; color: #745cff; font-size: 6.5pt; font-weight: bold; letter-spacing: .8px; text-transform: uppercase; }
        .recommendation { margin-top: 2mm; padding: 3mm; background: #efffc2; }
        .methodology { page-break-before: always; }
        .methodology li { margin-bottom: 2mm; }
        .footer-note { position: fixed; right: 0; bottom: -12mm; left: 0; color: #7b817d; font-size: 6.5pt; text-align: center; }
    </style>
</head>
<body>
<div class="footer-note">WebIgnitors Website Intelligence · info@webignitors.in · +91 82619 73645</div>

<section class="cover">
    <div class="cover-accent"></div>
    <div class="brand"><span class="brand-mark">WI</span>WebIgnitors</div>
    <div style="margin-top: 31mm">
        <span class="cover-kicker">Website Intelligence Report</span>
        <h1>{{ $report->website_title ?: $report->domain }}<br><span>measured clearly.</span></h1>
        <p class="cover-url">{{ $report->final_url ?: $report->requested_url }}</p>
    </div>
    <div class="cover-score"><strong>{{ data_get($report->scores, 'overall', '—') }}</strong><span>Overall score</span></div>
    <div class="cover-meta">Prepared for {{ $report->user->name }} · {{ $report->completed_at?->format('d F Y') }} · Report {{ substr($report->uuid, 0, 8) }}</div>
</section>

<section>
    <div class="page-title">
        <span class="eyebrow">Executive summary</span>
        <h2>{{ data_get($report->summary, 'rating') }}</h2>
        <p>{{ data_get($report->summary, 'narrative') }}</p>
    </div>
    <table class="score-table">
        @foreach (array_chunk($report->scores ?? [], 4, true) as $row)
            <tr>
                @foreach ($row as $label => $score)
                    <td><strong>{{ $score }}</strong><span>{{ str_replace('_', ' ', $label) }}</span></td>
                @endforeach
                @for ($i = count($row); $i < 4; $i++)<td></td>@endfor
            </tr>
        @endforeach
    </table>
    <div class="summary">
        <h3>What this means</h3>
        <p>The audit identified <strong>{{ data_get($report->summary, 'finding_count', $report->findings->count()) }}</strong> observations. Work through the high-impact priorities first, then use the detailed category sections as an implementation backlog.</p>
        <p style="margin-bottom: 0"><strong>Strongest signals:</strong> {{ implode(', ', data_get($report->summary, 'strengths', [])) }}</p>
    </div>

    <div class="page-title" style="margin-top: 12mm">
        <span class="eyebrow">Priority roadmap</span>
        <h2>The first moves to make.</h2>
    </div>
    @foreach (array_slice($report->top_recommendations ?? [], 0, 10) as $index => $priority)
        <table class="priority">
            <tr>
                <td class="priority-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <span class="tag tag-{{ $priority['severity'] }}">{{ $priority['severity'] }}</span>
                    <span class="tag">{{ $priority['category'] }}</span>
                    <span class="tag">Impact {{ $priority['impact'] }}</span>
                    <span class="tag">Effort {{ $priority['effort'] }}</span>
                    <h3>{{ $priority['title'] }}</h3>
                    <p>{{ $priority['recommendation'] }}</p>
                </td>
            </tr>
        </table>
    @endforeach
</section>

@if ($page)
    <section style="page-break-before: always">
        <div class="page-title">
            <span class="eyebrow">Measured evidence</span>
            <h2>Performance and page structure.</h2>
            <p>Laboratory results describe this controlled test. Real-user CrUX data is included when Google has sufficient public samples.</p>
        </div>
        <table class="metric-table">
            <thead><tr><th>Metric</th><th>Mobile</th><th>Desktop</th></tr></thead>
            <tbody>
            @foreach (['first-contentful-paint', 'largest-contentful-paint', 'speed-index', 'total-blocking-time', 'cumulative-layout-shift', 'total-byte-weight'] as $metric)
                <tr>
                    <td>{{ data_get($page->metrics, "mobile.{$metric}.label", str_replace('-', ' ', ucfirst($metric))) }}</td>
                    <td>{{ data_get($page->metrics, "mobile.{$metric}.display", '—') }}</td>
                    <td>{{ data_get($page->metrics, "desktop.{$metric}.display", '—') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="metric-table">
            <thead><tr><th>Page structure</th><th>Measured value</th></tr></thead>
            <tbody>
                <tr><td>DOM elements</td><td>{{ number_format(data_get($page->meta, 'dom_nodes', 0)) }}</td></tr>
                <tr><td>Images / missing alt</td><td>{{ data_get($page->meta, 'images', 0) }} / {{ data_get($page->meta, 'images_missing_alt', 0) }}</td></tr>
                <tr><td>Scripts / stylesheets</td><td>{{ data_get($page->meta, 'scripts', 0) }} / {{ data_get($page->meta, 'stylesheets', 0) }}</td></tr>
                <tr><td>HTML validation errors</td><td>{{ data_get($page->metrics, 'html_validation.errors', 'Not available') }}</td></tr>
                <tr><td>MDN security grade</td><td>{{ data_get($page->metrics, 'security.grade', 'Not available') }}</td></tr>
            </tbody>
        </table>
        @if ($screenshots['desktop'])
            <span class="eyebrow">Desktop evidence</span>
            <br><img class="screenshot" src="{{ $screenshots['desktop'] }}" alt="">
        @endif
        @if ($screenshots['mobile'])
            <span class="eyebrow">Mobile evidence</span>
            <br><img class="screenshot" src="{{ $screenshots['mobile'] }}" alt="">
        @endif
    </section>
@endif

@foreach ($findingsByCategory as $category => $findings)
    <section class="category">
        <div class="category-heading">
            <h2>{{ ucwords(str_replace(['-', '_'], ' ', $category)) }}</h2>
            <strong>{{ data_get($report->scores, $category, '—') }}</strong>
        </div>
        @foreach ($findings as $finding)
            <article class="finding">
                <span class="tag tag-{{ $finding->severity }}">{{ $finding->severity }}</span>
                <span class="tag">Impact {{ $finding->impact }}</span>
                <span class="tag">Effort {{ $finding->effort }}</span>
                <h3>{{ $finding->title }}</h3>
                <table class="finding-grid">
                    <tr>
                        <td><span class="finding-label">What it means</span>{{ $finding->description }}</td>
                        <td><span class="finding-label">Evidence</span>{{ $finding->evidence ?: 'No additional element-level evidence was returned.' }}</td>
                    </tr>
                </table>
                <div class="recommendation"><span class="finding-label">Recommended move</span>{{ $finding->recommendation }}</div>
                <p style="margin: 2mm 0 0; color: #7b817d; font-size: 7pt">Source: {{ $finding->source }}</p>
            </article>
        @endforeach
    </section>
@endforeach

<section class="methodology">
    <div class="page-title">
        <span class="eyebrow">Methodology and limitations</span>
        <h2>How to read this report.</h2>
    </div>
    <ul>
        <li>Performance results are controlled laboratory measurements and may change between runs, locations and devices.</li>
        <li>CrUX field data is shown only when Google has sufficient aggregated real-user samples for the URL.</li>
        <li>Automated accessibility checks identify many common issues but cannot replace manual keyboard and assistive-technology testing.</li>
        <li>Design and marketing scores use transparent heuristics covering hierarchy, mobile readiness, calls to action, proof and measurement—not subjective aesthetic judgement.</li>
        <li>Security checks are passive and focus on public browser-facing configuration. They are not penetration tests.</li>
        <li>Recommendations should be reviewed against business goals, analytics and technical constraints before implementation.</li>
    </ul>
    <div class="summary" style="margin-top: 12mm">
        <h3>Want help turning this into results?</h3>
        <p>WebIgnitors designs and builds websites, eCommerce, ERP, CRM, HRM, mobile products and automation systems.</p>
        <p style="margin-bottom: 0"><strong>info@webignitors.in · +91 82619 73645 · webignitors.in</strong></p>
    </div>
</section>
</body>
</html>
