@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<section class="auth-page">
    <div class="auth-orb" aria-hidden="true"></div>
    <div class="container">
        <div class="auth-card">
            <div class="auth-card-copy">
                <a class="auth-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> WebIgnitors</a>
                <span class="section-label text-white">Client intelligence</span>
                <h1>Your reports are waiting.</h1>
                <p>Sign in to review findings, track audits, compare scores and download private PDFs.</p>
                @if (session('pending_audit_url'))
                    <div class="pending-url">
                        <span>Ready to analyse</span>
                        <strong>{{ parse_url(session('pending_audit_url'), PHP_URL_HOST) }}</strong>
                    </div>
                @endif
            </div>
            <div class="auth-form-panel">
                <h2>Welcome back</h2>
                <p>New here? <a href="{{ route('register') }}">Create an account</a></p>
                @if (session('status'))<div class="alert alert-info">{{ session('status') }}</div>@endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <label class="form-label" for="password">Password</label>
                            <a class="auth-small-link" href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                        <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                    <label class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" value="1">
                        <span class="form-check-label">Keep me signed in</span>
                    </label>
                    <button class="btn btn-ink w-100" type="submit">Sign in <i class="bi bi-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
