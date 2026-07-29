@extends('layouts.app')

@section('title', 'Our Process')
@section('meta_description', 'See WebIgnitors’ clear, collaborative process from alignment and prototyping through delivery, launch, and continuous growth.')

@section('content')
<header class="page-hero">
    <div class="container">
        <div class="breadcrumb-mini"><a href="{{ route('home') }}">Home</a> / <span>Process</span></div>
        <h1 class="page-title">Less theatre.<br>More <span class="text-coral">forward motion.</span></h1>
        <p class="page-lead">A visible, collaborative process designed to reduce risk early, keep decisions close to the work, and turn learning into progress.</p>
    </div>
</header>

<section class="section-space-sm">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4 reveal">
                <span class="section-label">The rhythm</span>
                <h2 class="section-title fs-1">Four loops. One accountable team.</h2>
                <p class="section-copy fs-6">The details shift by project, but the logic stays constant: understand before expanding, make ideas tangible, ship in slices, and learn from reality.</p>
            </div>
            <div class="col-lg-8 reveal">
                <div class="process-list">
                    <div class="process-row">
                        <span class="process-num">01 / ALIGN</span>
                        <h3>Make the problem sharp</h3>
                        <p>Stakeholder sessions, user insight, evidence review, competitive context, technical constraints, and a shared definition of success.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">02 / CREATE</span>
                        <h3>Turn strategy into something testable</h3>
                        <p>Journeys, concepts, prototypes, content direction, and technical spikes make critical assumptions visible while change is still cheap.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">03 / DELIVER</span>
                        <h3>Build in visible increments</h3>
                        <p>Design, engineering, content, QA, and launch preparation move through small reviewable slices with regular demos.</p>
                    </div>
                    <div class="process-row">
                        <span class="process-num">04 / EVOLVE</span>
                        <h3>Use reality as the roadmap</h3>
                        <p>Analytics, user behavior, operational feedback, and commercial results guide improvements after release.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-ink">
    <div class="container">
        <div class="row align-items-end mb-5 g-4">
            <div class="col-lg-8 reveal">
                <span class="section-label text-white">What it feels like</span>
                <h2 class="section-title mb-0">No vanishing acts.</h2>
            </div>
            <div class="col-lg-4 reveal">
                <p class="mb-0 text-white-50">You stay close to the work without having to manage it. We create a simple operating rhythm and keep decisions moving.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">M</span>
                    <h3>Monday map</h3>
                    <p>What moved, what is next, what needs a decision, and what we learned.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">W</span>
                    <h3>Working session</h3>
                    <p>A focused collaboration on the decisions where your context matters most.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">F</span>
                    <h3>Friday demo</h3>
                    <p>Real progress you can see, use, challenge, and share with your team.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal">
                <div class="value-card">
                    <span class="value-num">24h</span>
                    <h3>Decision notes</h3>
                    <p>Concise written context keeps the project aligned across time zones.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal">
                <div class="story-card" style="background: var(--coral)">
                    <div class="story-orb" aria-hidden="true"></div>
                    <span class="story-word">SHIP</span>
                </div>
            </div>
            <div class="col-lg-6 reveal">
                <span class="section-label">Your side of the table</span>
                <h2 class="section-title fs-1">We ask for access, honesty, and timely decisions.</h2>
                <p class="text-soft">The strongest work is a collaboration. We bring process, outside perspective, creative range, and specialist execution. You bring customer context, business reality, and the courage to choose.</p>
                <div class="dark-feature mt-4">
                    <ul class="mt-0">
                        <li><i class="bi bi-check2-circle"></i><span>One empowered project owner</span></li>
                        <li><i class="bi bi-check2-circle"></i><span>Access to customers, data, and subject experts</span></li>
                        <li><i class="bi bi-check2-circle"></i><span>Consolidated feedback at agreed checkpoints</span></li>
                        <li><i class="bi bi-check2-circle"></i><span>Decisions tied back to the shared success measure</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-white-soft">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 reveal">
                <span class="section-label">Common questions</span>
                <h2 class="section-title fs-1">Before we begin.</h2>
            </div>
            <div class="col-lg-7 reveal">
                <div class="accordion faq" id="processFaq">
                    @foreach ([
                        ['How do projects usually start?', 'With a 30-minute fit call. If the challenge is a match, we recommend a focused discovery sprint or prepare a clear project proposal.'],
                        ['Who will be on our team?', 'A senior engagement lead remains accountable throughout, supported by the product, design, engineering, growth, or AI specialists the work actually needs.'],
                        ['How do you handle scope changes?', 'We make tradeoffs visible. Small shifts can move within the active backlog; meaningful changes come with a clear impact on timing, cost, and outcomes before you decide.'],
                        ['What tools do you use?', 'Typically Slack or Teams for communication, Notion or Linear for decisions and delivery, Figma for design, GitHub for code, and a shared analytics view after launch.'],
                    ] as [$question, $answer])
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#process{{ $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $question }}
                                </button>
                            </h3>
                            <div id="process{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#processFaq">
                                <div class="accordion-body">{{ $answer }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.cta', ['title' => 'A clear process starts with a candid conversation.', 'copy' => 'Tell us what is at stake, what you know, and where things feel uncertain.'])
@endsection
