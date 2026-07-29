<header class="page-hero service-hero {{ $theme }}">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-8">
                <div class="breadcrumb-mini"><a href="{{ route('services') }}">Services</a> / <span>{{ $eyebrow }}</span></div>
                <h1 class="page-title">{{ $headline }} <span class="text-coral">{{ $accent }}</span></h1>
                <p class="page-lead">{{ $lead }}</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a class="btn btn-ink" href="{{ route('contact', ['service' => $serviceKey]) }}">Discuss your project <i class="bi bi-arrow-up-right"></i></a>
                    <a class="btn btn-outline-ink" href="#capabilities">See capabilities <i class="bi bi-arrow-down"></i></a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="service-mark" aria-hidden="true"><i class="bi {{ $icon }}"></i></div>
            </div>
        </div>
    </div>
</header>

<section class="section-space-sm">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal">
                <span class="section-label">The opportunity</span>
                <h2 class="section-title fs-1">{{ $opportunityTitle }}</h2>
            </div>
            <div class="col-lg-7 reveal">
                @foreach ($opportunityCopy as $paragraph)
                    <p class="{{ $loop->last ? 'mb-0 fw-semibold' : 'text-soft' }}">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-white-soft" id="capabilities">
    <div class="container">
        <div class="row align-items-end mb-5 g-4">
            <div class="col-lg-8 reveal">
                <span class="section-label">What we deliver</span>
                <h2 class="section-title mb-0">{{ $deliverableTitle }}</h2>
            </div>
            <div class="col-lg-4 reveal">
                <p class="text-soft mb-0">{{ $deliverableCopy }}</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach ($deliverables as $deliverable)
                <div class="col-md-6 col-lg-4 reveal">
                    <article class="deliverable-card">
                        <i class="bi {{ $deliverable['icon'] }}" aria-hidden="true"></i>
                        <h4>{{ $deliverable['title'] }}</h4>
                        <p>{{ $deliverable['copy'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal">
                <div class="dark-feature">
                    <span class="section-label text-white">Included in every build</span>
                    <h2 class="section-title fs-1">{{ $featureTitle }}</h2>
                    <ul>
                        @foreach ($featureList as $feature)
                            <li><i class="bi bi-check2-circle"></i><span>{{ $feature }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 reveal">
                <span class="section-label">The playbook</span>
                <h2 class="section-title fs-1 mb-4">From ambiguity to impact.</h2>
                <div class="timeline">
                    @foreach ($steps as $step)
                        <div class="timeline-item">
                            <small>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ $step['label'] }}</small>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['copy'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space-sm">
    <div class="container">
        <div class="quote-card reveal">
            <blockquote>{{ $quote }}</blockquote>
            <div class="quote-author">
                <div class="quote-avatar">{{ $quoteInitials }}</div>
                <p><strong>{{ $quoteName }}</strong>{{ $quoteRole }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 reveal">
                <span class="section-label">Straight answers</span>
                <h2 class="section-title fs-1">A few things you may be wondering.</h2>
            </div>
            <div class="col-lg-7 reveal">
                <div class="accordion faq" id="serviceFaq">
                    @foreach ($faqs as $faq)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="faq{{ $loop->iteration }}">
                                    {{ $faq['question'] }}
                                </button>
                            </h3>
                            <div id="faq{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#serviceFaq">
                                <div class="accordion-body">{{ $faq['answer'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.cta', ['title' => $ctaTitle, 'copy' => $ctaCopy])
