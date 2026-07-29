@extends('layouts.app')

@section('title', 'About')
@section('meta_description', 'Meet WebIgnitors, a senior digital studio where strategy, design, engineering, AI, and growth work as one.')

@section('content')
<header class="page-hero">
    <div class="container">
        <div class="breadcrumb-mini"><a href="{{ route('home') }}">Home</a> / <span>About</span></div>
        <h1 class="page-title">Small team.<br>Serious <span class="text-coral">spark.</span></h1>
        <p class="page-lead">We are strategists, designers, developers, and growth minds who believe the best digital work happens when the walls between disciplines come down.</p>
    </div>
</header>

<section class="section-space-sm">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal">
                <div class="story-card">
                    <div class="story-orb" aria-hidden="true"></div>
                    <span class="story-word">MAKE</span>
                </div>
            </div>
            <div class="col-lg-6 reveal">
                <span class="section-label">Our story</span>
                <h2 class="section-title fs-1">Born from a healthy impatience with forgettable digital work.</h2>
                <p class="text-soft">WebIgnitors started with a simple frustration: ambitious companies were choosing between beautiful work that did not perform and performant work nobody remembered.</p>
                <p class="text-soft">So we built the studio we wanted to hire—senior, cross-functional, candid, and focused on outcomes. Today we partner with teams from first idea to growth stage, turning complicated challenges into clear and useful digital products.</p>
                <p class="mb-0 fw-semibold">We stay intentionally compact. The people in the room are the people doing the work.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 reveal">
                <span class="section-label">What we believe</span>
                <h2 class="section-title">Principles with some teeth.</h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">01</span>
                    <h3>Clarity wins</h3>
                    <p>Good strategy should make the next move obvious—not fill a presentation with fog.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">02</span>
                    <h3>Craft converts</h3>
                    <p>Details shape trust. We sweat the interactions, language, performance, and finish.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">03</span>
                    <h3>Show the work</h3>
                    <p>No black boxes. You see progress early, test often, and know where every decision came from.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">04</span>
                    <h3>Outcomes first</h3>
                    <p>A launch only matters when it moves a real signal: adoption, efficiency, revenue, or trust.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-white-soft">
    <div class="container">
        <div class="row align-items-end mb-5 g-4">
            <div class="col-lg-8 reveal">
                <span class="section-label">The crew</span>
                <h2 class="section-title mb-0">Meet the people behind the spark.</h2>
            </div>
            <div class="col-lg-4 reveal">
                <p class="text-soft mb-0">A co-founder-led core team bringing strategy, design, engineering, mobile, and growth into one focused studio.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal">
                <article class="team-card team-violet">
                    <div class="team-portrait"></div>
                    <div class="team-details">
                        <span class="team-badge">Co-founder</span>
                        <h4>Anuj Shukla</h4>
                        <p>Co-founder & strategic direction</p>
                    </div>
                </article>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <article class="team-card team-coral">
                    <div class="team-portrait"></div>
                    <div class="team-details">
                        <span class="team-badge">Co-founder</span>
                        <h4>Nilesh Dubey</h4>
                        <p>Co-founder & engineering direction</p>
                    </div>
                </article>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <article class="team-card">
                    <div class="team-portrait"></div>
                    <div class="team-details">
                        <span class="team-badge">Crew</span>
                        <h4>Ayush Shukla</h4>
                        <p>Mobile apps & product delivery</p>
                    </div>
                </article>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <article class="team-card team-violet">
                    <div class="team-portrait"></div>
                    <div class="team-details">
                        <span class="team-badge">Crew</span>
                        <h4>Vikash Shukla</h4>
                        <p>Digital marketing & growth</p>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal">
                <span class="section-label">Built everywhere</span>
                <h2 class="section-title fs-1">Distributed by design. Together by habit.</h2>
            </div>
            <div class="col-lg-7 reveal">
                <p class="section-copy mb-4">We work across time zones with a deliberate communication rhythm: shared working sessions, concise async updates, and a single source of truth. Geography stays flexible; accountability does not.</p>
                <div class="capability-list">
                    <span>Bengaluru</span><span>London</span><span>Toronto</span><span>Dubai</span><span>Remote-first</span><span>Global clients</span>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.cta', ['title' => 'Want us in your corner?', 'copy' => 'Tell us what you are trying to change. We will tell you honestly where we can help.'])
@endsection
