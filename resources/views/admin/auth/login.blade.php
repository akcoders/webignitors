@extends('layouts.app')

@section('title', 'Administrator Sign In')
@section('meta_description', 'Secure WebIgnitors administrator access.')

@section('content')
<section class="auth-page admin-login-page">
    <div class="auth-orb" aria-hidden="true"></div>
    <div class="container">
        <div class="auth-card">
            <div class="auth-card-copy">
                <a class="auth-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> WebIgnitors</a>
                <span class="section-label text-white">Restricted operations</span>
                <h1>Control room access.</h1>
                <p>Monitor audits, queue health, customer accounts and incoming project opportunities from one private workspace.</p>
                <div class="admin-security-note">
                    <i class="bi bi-shield-lock"></i>
                    <span>Administrator accounts are created only from the secure server console.</span>
                </div>
            </div>
            <div class="auth-form-panel">
                <span class="admin-login-mark"><i class="bi bi-command"></i> Admin console</span>
                <h2>Sign in securely</h2>
                <p>Use your administrator credentials.</p>
                @if (session('status'))<div class="alert alert-info">{{ session('status') }}</div>@endif
                <form method="POST" action="{{ route('admin.login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="admin-email">Administrator email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="admin-email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="admin-password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="admin-password" name="password" type="password" autocomplete="current-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <label class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" value="1">
                        <span class="form-check-label">Keep this trusted device signed in</span>
                    </label>
                    <x-turnstile action="admin_login" />
                    <button class="btn btn-ink w-100" type="submit">Enter control room <i class="bi bi-arrow-right"></i></button>
                </form>
                <a class="admin-client-link" href="{{ route('login') }}">Looking for client sign in?</a>
            </div>
        </div>
    </div>
</section>
@endsection
