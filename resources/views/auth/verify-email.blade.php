@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<section class="auth-page auth-page-simple">
    <div class="container">
        <div class="auth-single text-center">
            <div class="verify-icon"><i class="bi bi-envelope-check"></i></div>
            <span class="section-label">One final step</span>
            <h1>Verify your email.</h1>
            <p>We sent a verification link to <strong>{{ auth()->user()->email }}</strong>. Verification protects your private reports and unlocks PDF downloads.</p>
            @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn btn-ink w-100" type="submit">Resend verification email</button>
            </form>
            <a class="auth-return" href="{{ route('dashboard') }}">Continue to dashboard</a>
        </div>
    </div>
</section>
@endsection
