@extends('layouts.app')

@section('title', 'Selected Work')
@section('meta_description', 'Explore selected WebIgnitors work across digital products, websites, mobile apps, growth programs, and practical AI systems.')

@section('content')
<header class="page-hero">
    <div class="container">
        <div class="breadcrumb-mini"><a href="{{ route('home') }}">Home</a> / <span>Work</span></div>
        <h1 class="page-title">Proof is in<br>the <span class="text-coral">momentum.</span></h1>
        <p class="page-lead">A selection of digital products and growth systems shaped around a real constraint—and judged by what changed after launch.</p>
    </div>
</header>

<section class="section-space-sm">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7 reveal">
                <article class="project-card h-100">
                    <div class="project-visual">
                        <span class="project-tag">FINTECH / WEB PLATFORM</span>
                        <div class="project-window">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div><div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="d-flex justify-content-between gap-3 align-items-start">
                            <div><h3>Northstar Finance</h3><div class="project-meta"><span>Strategy</span><span>Product design</span><span>Laravel</span></div></div>
                            <strong class="text-coral fs-5">+52% activation</strong>
                        </div>
                    </div>
                </article>
            </div>
            <div class="col-lg-5 reveal">
                <article class="project-card h-100">
                    <div class="project-visual coral">
                        <span class="project-tag">WELLNESS / MOBILE</span>
                        <div class="project-window mobile">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div><div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>Pulse Daily</h3>
                        <div class="project-meta"><span>iOS & Android</span><span>UX/UI</span><span>120k users</span></div>
                    </div>
                </article>
            </div>

            <div class="col-lg-5 reveal">
                <article class="project-card h-100">
                    <div class="project-visual aqua">
                        <span class="project-tag">LOGISTICS / AI</span>
                        <div class="project-window mobile">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div><div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>Align Freight</h3>
                        <div class="project-meta"><span>AI workflow</span><span>RAG</span><span>300 hrs saved/month</span></div>
                    </div>
                </article>
            </div>
            <div class="col-lg-7 reveal">
                <article class="project-card h-100">
                    <div class="project-visual lime">
                        <span class="project-tag">B2B SAAS / GROWTH</span>
                        <div class="project-window">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div><div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="d-flex justify-content-between gap-3 align-items-start">
                            <div><h3>Fieldwork OS</h3><div class="project-meta"><span>Positioning</span><span>SEO</span><span>Paid media</span></div></div>
                            <strong class="text-coral fs-5">2.4× pipeline</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-lg-6 reveal">
                <article class="project-card h-100">
                    <div class="project-visual ink">
                        <span class="project-tag text-white border-white">CLIMATE / COMMERCE</span>
                        <div class="project-window">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div><div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>GoodGround Market</h3>
                        <div class="project-meta"><span>Brand</span><span>Shopify</span><span>Conversion</span><span>+41% AOV</span></div>
                    </div>
                </article>
            </div>
            <div class="col-lg-6 reveal">
                <article class="project-card h-100">
                    <div class="project-visual coral">
                        <span class="project-tag">EDTECH / PLATFORM</span>
                        <div class="project-window">
                            <div class="project-window-top"></div>
                            <div class="project-ui">
                                <div class="ui-heading"></div>
                                <div class="ui-line"></div><div class="ui-line short"></div>
                                <div class="ui-blocks"><span></span><span></span><span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>Curious Minds</h3>
                        <div class="project-meta"><span>UX research</span><span>Web app</span><span>AI tutor</span><span>4.8 rating</span></div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="row metric-row text-center">
            <div class="col-6 col-md-3 metric"><strong>60+</strong><span>Launches across 8 sectors</span></div>
            <div class="col-6 col-md-3 metric"><strong>92%</strong><span>Partner referral rate</span></div>
            <div class="col-6 col-md-3 metric"><strong>18</strong><span>Industry awards & features</span></div>
            <div class="col-6 col-md-3 metric"><strong>4.9/5</strong><span>Average partner rating</span></div>
        </div>
    </div>
</section>

<section class="section-space-sm">
    <div class="container">
        <div class="quote-card reveal">
            <blockquote>They challenged the brief in all the right places. The result was clearer, faster, and far more effective than the project we thought we needed.</blockquote>
            <div class="quote-author">
                <div class="quote-avatar"><i class="bi bi-chat-quote"></i></div>
                <p><strong>Client feedback</strong>Commerce leadership team</p>
            </div>
        </div>
    </div>
</section>

@include('partials.cta', ['title' => 'Want your project in the next chapter?', 'copy' => 'Let’s find the decision that unlocks the work—and build from there.'])
@endsection
