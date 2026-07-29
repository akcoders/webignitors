@extends('layouts.app')

@section('title', 'Free Website Intelligence Report')
@section('meta_description', 'Audit any public website for performance, design, accessibility, code quality, SEO, marketing and security. Get a detailed WebIgnitors report.')

@section('content')
<section class="audit-hero">
    <div class="audit-hero-grid" aria-hidden="true"></div>
    <span class="audit-hero-ghost" aria-hidden="true" data-parallax="0.04">ANALYSE</span>
    <div class="container position-relative">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 reveal">
                <div class="hero-kicker"><span class="pulse-dot"></span> WebIgnitors Website Intelligence</div>
                <h1>Your website,<br><span>explained.</span></h1>
                <p>One URL becomes a detailed, plain-language report across design, mobile layout, performance, JavaScript, CSS, accessibility, SEO, marketing and security.</p>

                <form class="audit-url-form" method="POST" action="{{ route('audit.store') }}">
                    @csrf
                    <div class="audit-url-field">
                        <i class="bi bi-globe2" aria-hidden="true"></i>
                        <label class="visually-hidden" for="audit-url">Website URL</label>
                        <input
                            id="audit-url"
                            name="url"
                            type="text"
                            inputmode="url"
                            value="{{ old('url') }}"
                            placeholder="yourwebsite.com"
                            autocomplete="url"
                            required
                        >
                        <button type="submit">Analyse website <i class="bi bi-arrow-up-right"></i></button>
                    </div>
                    <div class="audit-honeypot" aria-hidden="true">
                        <label for="audit-website">Leave this field empty</label>
                        <input id="audit-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>
                    @error('url')<div class="audit-form-error">{{ $message }}</div>@enderror
                    <p class="audit-form-note">
                        <i class="bi bi-shield-check"></i>
                        @auth
                            The report will be saved privately to your account.
                        @else
                            A free account is required to save and download the detailed report.
                        @endauth
                    </p>
                </form>

                <div class="audit-source-row" aria-label="Audit data sources">
                    <span>Lighthouse</span><span>CrUX</span><span>W3C</span><span>MDN</span><span>WebIgnitors</span>
                </div>
            </div>

            <div class="col-lg-5 reveal">
                <div class="audit-preview" data-tilt>
                    <div class="audit-preview-top">
                        <span class="audit-live"><i></i> Sample analysis</span>
                        <span>webignitors.in</span>
                    </div>
                    <div class="audit-score-orbit">
                        <div><strong>82</strong><span>Overall</span></div>
                    </div>
                    <div class="audit-mini-scores">
                        @foreach ([['Performance', 74], ['SEO', 91], ['Design', 86], ['Marketing', 78]] as [$label, $score])
                            <div>
                                <span>{{ $label }}</span><strong>{{ $score }}</strong>
                                <i><b style="width: {{ $score }}%"></b></i>
                            </div>
                        @endforeach
                    </div>
                    <div class="audit-preview-finding">
                        <span>HIGH IMPACT</span>
                        <p>Reduce unused JavaScript to improve mobile interaction speed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="audit-categories section-space-sm">
    <div class="container">
        <div class="row align-items-end g-4 mb-5">
            <div class="col-lg-8 reveal">
                <span class="section-label">Eight lenses. One clear plan.</span>
                <h2 class="section-title mb-0">A report people can actually use.</h2>
            </div>
            <div class="col-lg-4 reveal">
                <p class="text-soft mb-0">Every finding includes evidence, business impact, implementation effort and a practical recommendation.</p>
            </div>
        </div>
        <div class="audit-category-grid">
            @foreach ([
                ['bi-speedometer2', 'Performance', 'Core Web Vitals, rendering, assets, caching and page weight.'],
                ['bi-bezier2', 'Design & layout', 'Responsive structure, hierarchy, stability, typography and interaction.'],
                ['bi-braces', 'JavaScript & CSS', 'Unused code, execution cost, third-party scripts and complexity.'],
                ['bi-universal-access', 'Accessibility', 'WCAG-oriented checks, labels, contrast, semantics and navigation.'],
                ['bi-search', 'Technical SEO', 'Metadata, headings, canonicals, structured data and indexability.'],
                ['bi-bullseye', 'Marketing', 'Value proposition, CTAs, proof, analytics and conversion paths.'],
                ['bi-shield-lock', 'Security', 'HTTPS, defensive headers, mixed content and trust configuration.'],
                ['bi-robot', 'Automation', 'Practical opportunities to improve leads, service and operations.'],
            ] as [$icon, $title, $copy])
                <article class="audit-category-card reveal" data-tilt>
                    <i class="bi {{ $icon }}"></i>
                    <h3>{{ $title }}</h3>
                    <p>{{ $copy }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="audit-method section-space">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 reveal">
                <span class="section-label text-white">From signal to roadmap</span>
                <h2>Not another score with no next step.</h2>
                <p>Raw audit data is normalised into plain language and ranked by severity, business impact and implementation effort.</p>
            </div>
            <div class="col-lg-7">
                @foreach ([
                    ['01', 'Measure', 'Mobile and desktop audits collect technical, content and real-user signals.'],
                    ['02', 'Interpret', 'WebIgnitors turns tool output into evidence-led findings and transparent scores.'],
                    ['03', 'Prioritise', 'The report separates urgent risks, quick wins and longer-term growth work.'],
                    ['04', 'Download', 'Your complete branded PDF and report history remain private in your account.'],
                ] as [$number, $title, $copy])
                    <div class="audit-method-row reveal">
                        <span>{{ $number }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section-space-sm">
    <div class="container">
        <div class="audit-final-cta reveal">
            <div>
                <span>READY WHEN YOU ARE</span>
                <h2>Find out what your website is leaving on the table.</h2>
            </div>
            <a class="btn btn-ink" href="#audit-url" data-magnetic>Start free audit <i class="bi bi-arrow-up"></i></a>
        </div>
    </div>
</section>
@endsection
