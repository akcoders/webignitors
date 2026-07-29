@extends('layouts.app')

@section('title', 'Creative Development & Growth Agency')
@section('meta_description', 'WebIgnitors builds eCommerce, ERP, CRM, HRM, complex web applications, mobile products, marketing systems, and AI automation for measurable business growth.')

@section('content')
<section class="hero">
    <div class="hero-atmosphere" aria-hidden="true">
        <div class="hero-grid-plane" data-parallax="0.035"></div>
        <div class="hero-orb hero-orb-violet" data-parallax="-0.12"></div>
        <div class="hero-orb hero-orb-lime" data-parallax="0.16"></div>
        <div class="hero-wire-ring" data-parallax="-0.08"></div>
        <span class="hero-ghost" data-parallax="0.06">IGNITE</span>
        <span class="hero-coordinate hero-coordinate-one">19.0760° N / 72.8777° E</span>
        <span class="hero-coordinate hero-coordinate-two">STRATEGY → DESIGN → CODE → GROWTH</span>
    </div>
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 hero-copy" data-parallax="-0.025">
                <div class="hero-kicker">
                    <span class="pulse-dot" aria-hidden="true"></span>
                    Independent digital studio · India / Everywhere
                </div>
                <h1 class="hero-title">
                    We build<br>
                    <span class="hero-outline">digital</span> <span class="scribble">momentum.</span>
                </h1>
                <p class="hero-lead">
                    High-voltage websites, business platforms, iOS & Android products, growth systems, and useful AI—built to unlock more of your business through automation.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-ink" href="{{ route('contact') }}" data-magnetic>
                        Start a project <i class="bi bi-arrow-up-right"></i>
                    </a>
                    <a class="btn btn-outline-ink" href="{{ route('work') }}" data-magnetic>
                        Explore our work <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="hero-proof">
                    <div class="avatar-stack" aria-hidden="true">
                        <span class="mini-avatar">AS</span>
                        <span class="mini-avatar">ND</span>
                        <span class="mini-avatar">AY</span>
                        <span class="mini-avatar">VS</span>
                    </div>
                    <p><strong class="d-block text-dark">Co-founder-led, specialist crew</strong>from first launch to next stage</p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="creative-stage" aria-label="Abstract preview of a website interface">
                    <div class="orbit" aria-hidden="true"></div>
                    <div class="stage-cross" aria-hidden="true">+</div>
                    <div class="stage-pill stage-pill-one" aria-hidden="true">Laravel</div>
                    <div class="stage-pill stage-pill-two" aria-hidden="true">AI native</div>
                    <div class="float-badge top" aria-hidden="true">Design<br>that moves</div>
                    <div class="float-badge bottom" aria-hidden="true">Built to<br>perform</div>
                    <div class="browser-card" data-tilt>
                        <div class="browser-top" aria-hidden="true"><span></span><span></span><span></span></div>
                        <div class="browser-screen">
                            <div class="screen-nav">
                                <span class="screen-logo"></span>
                                <div class="screen-pills"><span></span><span></span><span></span></div>
                            </div>
                            <div class="screen-hero">
                                <h4>Better ideas deserve better launches.</h4>
                                <p>Strategy, systems, and stories engineered to make an impact.</p>
                                <div class="screen-orb" aria-hidden="true"></div>
                            </div>
                            <div class="screen-grid">
                                <div class="screen-tile">
                                    <span class="screen-line"></span>
                                    <span class="screen-line short"></span>
                                </div>
                                <div class="screen-tile coral">
                                    <span class="screen-line"></span>
                                    <span class="screen-line short"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="marquee-strip" aria-hidden="true">
    <div class="marquee-track">
        @foreach (['Web experiences', 'eCommerce', 'ERP & CRM', 'Mobile products', 'AI automation', 'HRM systems', 'Complex web apps', 'Growth engines', 'Web experiences', 'eCommerce', 'ERP & CRM', 'AI automation'] as $item)
            <span>{{ $item }}</span>
        @endforeach
    </div>
</div>

<section class="manifesto-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="manifesto-sticky reveal">
                    <span class="section-label text-white">Our frequency</span>
                    <h2>Not another<br>quiet agency.</h2>
                    <p>We fuse strategy, visual culture, engineering, and experimentation into work with a pulse.</p>
                    <a class="btn btn-lime mt-3" href="{{ route('about') }}" data-magnetic>
                        Meet the crew <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="manifesto-words" aria-label="Make. Move. Matter.">
                    <div class="manifesto-word reveal" data-parallax="-0.04">
                        <span>01</span><strong>MAKE</strong><i>ideas tangible</i>
                    </div>
                    <div class="manifesto-word reveal is-lime" data-parallax="0.05">
                        <span>02</span><strong>MOVE</strong><i>people to act</i>
                    </div>
                    <div class="manifesto-word reveal is-coral" data-parallax="-0.035">
                        <span>03</span><strong>MATTER</strong><i>to the business</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space-sm">
    <div class="container">
        <div class="row metric-row text-center">
            <div class="col-6 col-md-3 metric">
                <strong>04</strong>
                <span>Connected disciplines</span>
            </div>
            <div class="col-6 col-md-3 metric">
                <strong>02</strong>
                <span>Hands-on co-founders</span>
            </div>
            <div class="col-6 col-md-3 metric">
                <strong>360°</strong>
                <span>Digital capability</span>
            </div>
            <div class="col-6 col-md-3 metric">
                <strong>∞</strong>
                <span>Room to grow</span>
            </div>
        </div>
    </div>
</section>

<section class="section-space" id="services">
    <div class="container">
        <div class="row align-items-end mb-5 g-4">
            <div class="col-lg-8 reveal">
                <span class="section-label">What we ignite</span>
                <h2 class="section-title mb-0">One studio. Every digital lever.</h2>
            </div>
            <div class="col-lg-4 reveal">
                <p class="text-soft mb-0">No relay races between scattered vendors. Strategy, design, engineering, and growth work together from day one.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7 reveal">
                <a class="bento-card violet h-100" href="{{ route('services.web') }}">
                    <span class="bento-index">01 / DEVELOPMENT</span>
                    <span class="shape-code" aria-hidden="true">&lt;/&gt;</span>
                    <div class="service-icon"><i class="bi bi-window"></i></div>
                    <h3>Websites & web platforms</h3>
                    <p>Fast, accessible digital experiences—from expressive marketing sites to eCommerce, ERP, CRM, HRM, and complex Laravel platforms.</p>
                    <span class="link-arrow">Explore web development <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
            <div class="col-lg-5 reveal">
                <a class="bento-card lime h-100" href="{{ route('services.mobile') }}">
                    <span class="bento-index">02 / MOBILE</span>
                    <span class="shape-code" aria-hidden="true">⌁</span>
                    <div class="service-icon"><i class="bi bi-phone"></i></div>
                    <h3>iOS & Android apps</h3>
                    <p>Native-feeling products people keep on their home screen.</p>
                    <span class="link-arrow">Explore app development <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
            <div class="col-lg-5 reveal">
                <a class="bento-card coral h-100" href="{{ route('services.marketing') }}">
                    <span class="bento-index">03 / GROWTH</span>
                    <span class="shape-code" aria-hidden="true">↗</span>
                    <div class="service-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <h3>Digital marketing</h3>
                    <p>SEO, content, paid media, and conversion systems that turn attention into durable growth.</p>
                    <span class="link-arrow">Explore growth <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
            <div class="col-lg-7 reveal">
                <a class="bento-card dark h-100" href="{{ route('services.ai') }}">
                    <span class="bento-index">04 / INTELLIGENCE</span>
                    <span class="shape-code" aria-hidden="true">✦</span>
                    <div class="service-icon"><i class="bi bi-stars"></i></div>
                    <h3>Useful AI, integrated</h3>
                    <p>Automations, copilots, search, and smart workflows designed around your real team—not a trend deck.</p>
                    <span class="link-arrow">Explore AI integration <i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="systems-speciality section-space">
    <div class="systems-atmosphere" aria-hidden="true">
        <span class="systems-ring systems-ring-one" data-parallax="-0.06"></span>
        <span class="systems-ring systems-ring-two" data-parallax="0.045"></span>
        <span class="systems-ghost" data-parallax="0.025">AUTOMATE</span>
    </div>
    <div class="container position-relative">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <div class="systems-intro reveal">
                    <span class="section-label text-white">Our speciality</span>
                    <h2>Serious systems for businesses ready to move.</h2>
                    <p>We design and engineer the operational platforms behind modern businesses—from the storefront customers see to the systems your team runs every day.</p>
                    <div class="automation-manifesto">
                        <span>Our model</span>
                        <strong>Get the best from your business through thoughtful automation.</strong>
                    </div>
                    <a class="btn btn-lime mt-4" href="{{ route('contact') }}" data-magnetic>
                        Automate your business <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="systems-grid">
                    <article class="system-card system-card-coral reveal" data-tilt>
                        <span class="system-index">01</span>
                        <i class="bi bi-bag-check" aria-hidden="true"></i>
                        <h3>eCommerce</h3>
                        <p>Custom storefronts, marketplaces, subscriptions, payments, inventory, and fulfilment integrations.</p>
                    </article>
                    <article class="system-card system-card-lime reveal" data-tilt>
                        <span class="system-index">02</span>
                        <i class="bi bi-diagram-3" aria-hidden="true"></i>
                        <h3>ERP</h3>
                        <p>Connected operations across finance, inventory, purchasing, production, reporting, and approvals.</p>
                    </article>
                    <article class="system-card system-card-violet reveal" data-tilt>
                        <span class="system-index">03</span>
                        <i class="bi bi-people" aria-hidden="true"></i>
                        <h3>CRM</h3>
                        <p>Lead pipelines, customer intelligence, sales automation, service workflows, and useful dashboards.</p>
                    </article>
                    <article class="system-card reveal" data-tilt>
                        <span class="system-index">04</span>
                        <i class="bi bi-person-badge" aria-hidden="true"></i>
                        <h3>HRM</h3>
                        <p>Employee records, attendance, leave, payroll workflows, recruitment, and performance systems.</p>
                    </article>
                    <article class="system-card system-card-wide reveal" data-tilt>
                        <span class="system-index">05</span>
                        <div>
                            <i class="bi bi-braces-asterisk" aria-hidden="true"></i>
                            <h3>Complex web applications</h3>
                        </div>
                        <p>Secure, scalable Laravel applications, portals, SaaS products, APIs, and tailored workflow engines built around the way your business actually works.</p>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-white-soft">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-5 reveal">
            <div>
                <span class="section-label">Selected sparks</span>
                <h2 class="section-title mb-0">Work with fingerprints.</h2>
            </div>
            <a class="link-arrow" href="{{ route('work') }}">See all projects <i class="bi bi-arrow-up-right"></i></a>
        </div>

        <div class="row g-4">
            <div class="col-lg-7 reveal">
                <article class="project-card h-100">
                    <div class="project-visual">
                        <span class="project-tag">FINTECH PLATFORM</span>
                        <div class="project-window">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div>
                                <div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>Northstar Finance</h3>
                        <div class="project-meta">
                            <span>Product strategy</span><span>UX/UI</span><span>Laravel</span><span>+52% activation</span>
                        </div>
                    </div>
                </article>
            </div>
            <div class="col-lg-5 reveal">
                <article class="project-card h-100">
                    <div class="project-visual coral">
                        <span class="project-tag">WELLNESS APP</span>
                        <div class="project-window mobile">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div>
                                <div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>Pulse Daily</h3>
                        <div class="project-meta">
                            <span>iOS & Android</span><span>Product design</span><span>120k users</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4 reveal">
                <span class="section-label">How we work</span>
                <h2 class="section-title fs-1">Clear enough to trust. Fast enough to matter.</h2>
                <p class="section-copy fs-6">Short feedback loops, senior hands on the work, and no mystery phase where your project disappears.</p>
                <a class="link-arrow mt-3" href="{{ route('process') }}">See our full process <i class="bi bi-arrow-up-right"></i></a>
            </div>
            <div class="col-lg-8 reveal">
                <div class="process-list">
                    <div class="process-row">
                        <span class="process-num">01 / ALIGN</span>
                        <h3>Find the sharpest problem</h3>
                        <p>We unpack the audience, business model, risks, and success signal before pixels or code.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">02 / CREATE</span>
                        <h3>Make the idea tangible</h3>
                        <p>We prototype the critical journey and shape a distinctive visual direction around it.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">03 / BUILD</span>
                        <h3>Engineer for momentum</h3>
                        <p>Our developers ship in visible increments with quality, speed, and future change in mind.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">04 / GROW</span>
                        <h3>Learn, tune, compound</h3>
                        <p>Launch is a starting line. We watch real behavior and improve what drives the outcome.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space-sm">
    <div class="container">
        <div class="quote-card reveal">
            <blockquote>WebIgnitors didn’t just ship our platform. They found the story inside it—and turned that story into our fastest quarter of growth.</blockquote>
            <div class="quote-author">
                <div class="quote-avatar"><i class="bi bi-chat-quote"></i></div>
                <p><strong>Client feedback</strong>Fintech product team</p>
            </div>
        </div>
    </div>
</section>

@include('partials.cta', [
    'title' => 'Got a business problem worth obsessing over?',
    'copy' => 'Give us the messy version. We’ll help you turn it into a focused, buildable next move.'
])
@endsection
