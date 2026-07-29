@extends('layouts.app')

@section('title', 'Start a Project')
@section('meta_description', 'Tell WebIgnitors about your website, mobile app, digital marketing, product strategy, or AI integration project.')

@section('content')
<section class="contact-wrap">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="contact-info">
                    <div class="breadcrumb-mini"><a href="{{ route('home') }}">Home</a> / <span>Contact</span></div>
                    <h1>Let’s make<br>some <span class="text-coral">noise.</span></h1>
                    <p class="page-lead mb-5">Tell us what you are building, fixing, or trying to unlock. We’ll reply within one business day with a useful next step.</p>

                    <div class="contact-direct">
                        <small>Email us directly</small>
                        <a href="mailto:info@webignitors.in">info@webignitors.in</a>
                    </div>
                    <div class="contact-direct">
                        <small>Call</small>
                        <a href="tel:+918261973645">+91 82619 73645</a>
                    </div>
                    <div class="contact-direct">
                        <small>Working globally</small>
                        <span>Mon–Fri · UTC−5 to UTC+5:30</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="contact-form-card">
                    @if (session('success'))
                        <div class="success-alert" role="status">
                            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <span class="section-label">Project brief</span>
                        <h2 class="fs-2 mb-2">Give us the useful details.</h2>
                        <p class="text-soft mb-0">Rough is completely fine. Fields marked * are required.</p>
                    </div>

                    <form method="POST" action="{{ route('contact.store') }}" data-inquiry-form novalidate>
                        @csrf
                        <div class="honeypot" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Name *</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Work email *</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="company">Company</label>
                                <input class="form-control @error('company') is-invalid @enderror" type="text" id="company" name="company" value="{{ old('company') }}" autocomplete="organization">
                                @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Phone</label>
                                <input class="form-control @error('phone') is-invalid @enderror" type="tel" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="service">Interested in *</label>
                                <select class="form-select @error('service') is-invalid @enderror" id="service" name="service" required>
                                    <option value="">Choose a service</option>
                                    @foreach ([
                                        'web-development' => 'Web development',
                                        'ecommerce' => 'eCommerce platform',
                                        'erp' => 'ERP system',
                                        'crm' => 'CRM platform',
                                        'hrm' => 'HRM software',
                                        'complex-web-application' => 'Complex web application',
                                        'business-automation' => 'Business automation',
                                        'mobile-apps' => 'iOS & Android app',
                                        'digital-marketing' => 'Digital marketing',
                                        'ai-integration' => 'AI integration',
                                        'product-strategy' => 'Product strategy',
                                        'other' => 'Something else',
                                    ] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('service', request('service')) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="budget">Approx. budget *</label>
                                <select class="form-select @error('budget') is-invalid @enderror" id="budget" name="budget" required>
                                    <option value="">Choose a range</option>
                                    <option value="under-5k" @selected(old('budget') === 'under-5k')>Under $5k</option>
                                    <option value="5k-15k" @selected(old('budget') === '5k-15k')>$5k–$15k</option>
                                    <option value="15k-40k" @selected(old('budget') === '15k-40k')>$15k–$40k</option>
                                    <option value="40k-plus" @selected(old('budget') === '40k-plus')>$40k+</option>
                                    <option value="not-sure" @selected(old('budget') === 'not-sure')>Not sure yet</option>
                                </select>
                                @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="message">What are you trying to make happen? *</label>
                                    <small class="text-soft" data-message-count>0 / 3000</small>
                                </div>
                                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" maxlength="3000" placeholder="The context, goal, timing, and biggest unknown are all useful…" required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button class="btn btn-ink w-100" type="submit">
                                    Send project brief <i class="bi bi-arrow-up-right"></i>
                                </button>
                                <p class="text-soft text-center mt-3 mb-0" style="font-size: .72rem">By submitting, you agree that we can reply about this project. No mailing lists. No nonsense.</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
