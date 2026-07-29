@extends('layouts.app')

@section('title', 'Create Account')
@section('meta_description', 'Create a private WebIgnitors account to run, save and download detailed website intelligence reports.')

@section('content')
<section class="auth-page">
    <div class="auth-orb" aria-hidden="true"></div>
    <div class="container">
        <div class="auth-card">
            <div class="auth-card-copy">
                <a class="auth-back" href="{{ route('audit.create') }}"><i class="bi bi-arrow-left"></i> Website audit</a>
                <span class="section-label text-white">Private report access</span>
                <h1>Turn the scan into a plan.</h1>
                <p>Create your free account to save reports, follow progress and download the complete branded PDF.</p>
                @if (session('pending_audit_url'))
                    <div class="pending-url">
                        <span>Ready to analyse</span>
                        <strong>{{ parse_url(session('pending_audit_url'), PHP_URL_HOST) }}</strong>
                    </div>
                @endif
            </div>
            <div class="auth-form-panel">
                <h2>Create account</h2>
                <p>Already registered? <a href="{{ route('login') }}">Sign in</a></p>
                @if (session('status'))<div class="alert alert-info">{{ session('status') }}</div>@endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="name">Your name</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Work email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small>At least 8 characters, including letters and numbers.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    </div>
                    <div class="audit-honeypot" aria-hidden="true">
                        <label for="register-website">Leave empty</label>
                        <input id="register-website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <button class="btn btn-ink w-100" type="submit">Create account <i class="bi bi-arrow-right"></i></button>
                </form>
                <p class="auth-terms">By creating an account, you agree to use the audit only for lawful evaluation of public websites.</p>
            </div>
        </div>
    </div>
</section>
@endsection
