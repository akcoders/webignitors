@extends('layouts.app')

@section('title', 'Services')
@section('meta_description', 'Explore WebIgnitors services across web development, iOS and Android apps, digital marketing, and practical AI integration.')

@section('content')
<header class="page-hero">
    <div class="container">
        <div class="breadcrumb-mini"><a href="{{ route('home') }}">Home</a> / <span>Services</span></div>
        <h1 class="page-title">From first click<br>to <span class="text-coral">full momentum.</span></h1>
        <p class="page-lead">We connect strategy, creative, technology, and growth so every part of your digital business pulls in the same direction.</p>
    </div>
</header>

<section class="section-space-sm">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6 reveal">
                <a class="bento-card violet h-100" href="{{ route('services.web') }}">
                    <span class="bento-index">01 / WEB DEVELOPMENT</span>
                    <span class="shape-code" aria-hidden="true">&lt;/&gt;</span>
                    <div class="service-icon"><i class="bi bi-window-stack"></i></div>
                    <h3>Websites & web platforms</h3>
                    <p>Marketing websites, e-commerce, Laravel applications, SaaS products, APIs, portals, and ongoing optimization.</p>
                    <span class="link-arrow">See web capabilities <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
            <div class="col-lg-6 reveal">
                <a class="bento-card lime h-100" href="{{ route('services.mobile') }}">
                    <span class="bento-index">02 / APP DEVELOPMENT</span>
                    <span class="shape-code" aria-hidden="true">⌁</span>
                    <div class="service-icon"><i class="bi bi-phone"></i></div>
                    <h3>iOS & Android applications</h3>
                    <p>Product strategy, UX/UI, native or cross-platform development, backend systems, launch, and iteration.</p>
                    <span class="link-arrow">See mobile capabilities <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
            <div class="col-lg-6 reveal">
                <a class="bento-card coral h-100" href="{{ route('services.marketing') }}">
                    <span class="bento-index">03 / DIGITAL MARKETING</span>
                    <span class="shape-code" aria-hidden="true">↗</span>
                    <div class="service-icon"><i class="bi bi-bullseye"></i></div>
                    <h3>Growth strategy & execution</h3>
                    <p>Technical SEO, content, paid search and social, lifecycle campaigns, analytics, and conversion rate optimization.</p>
                    <span class="link-arrow">See growth capabilities <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
            <div class="col-lg-6 reveal">
                <a class="bento-card dark h-100" href="{{ route('services.ai') }}">
                    <span class="bento-index">04 / AI INTEGRATION</span>
                    <span class="shape-code" aria-hidden="true">✦</span>
                    <div class="service-icon"><i class="bi bi-stars"></i></div>
                    <h3>Intelligent workflows & products</h3>
                    <p>AI readiness, copilots, RAG search, content operations, support automation, and model integration with guardrails.</p>
                    <span class="link-arrow">See AI capabilities <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-white-soft">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5 reveal">
                <span class="section-label">How to engage</span>
                <h2 class="section-title fs-1">A shape for every stage.</h2>
                <p class="section-copy fs-6">Start focused or bring us the whole challenge. We match the engagement to the risk, urgency, and clarity of the work.</p>
            </div>
            <div class="col-lg-7 reveal">
                <div class="process-list">
                    <div class="process-row">
                        <span class="process-num">SPRINT</span>
                        <h3>Find the move</h3>
                        <p>1–2 weeks to sharpen an idea, validate feasibility, map the journey, and define the right build.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">PROJECT</span>
                        <h3>Ship the thing</h3>
                        <p>A dedicated team to take a website, app, campaign, or AI workflow from brief to launch.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">PARTNER</span>
                        <h3>Build momentum</h3>
                        <p>An ongoing multidisciplinary pod for product iteration, experimentation, and measurable growth.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 reveal">
                <span class="section-label">Under the hood</span>
                <h2 class="section-title">Broad capability. One standard.</h2>
            </div>
        </div>
        <div class="capability-list reveal">
            @foreach (['Product strategy', 'UX research', 'Brand systems', 'UX/UI design', 'Laravel', 'React & Vue', 'Shopify', 'iOS', 'Android', 'Flutter', 'API design', 'Cloud infrastructure', 'Technical SEO', 'Paid media', 'Analytics', 'Conversion testing', 'OpenAI integration', 'RAG systems', 'Workflow automation', 'Ongoing support'] as $capability)
                <span>{{ $capability }}</span>
            @endforeach
        </div>
    </div>
</section>

@include('partials.cta', ['title' => 'Not sure which service you need?', 'copy' => 'Start with the business goal. We will help you choose the leanest path to it.'])
@endsection
