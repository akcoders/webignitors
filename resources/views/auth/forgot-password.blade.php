@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<section class="auth-page auth-page-simple">
    <div class="container">
        <div class="auth-single">
            <span class="section-label">Account recovery</span>
            <h1>Reset your password.</h1>
            <p>Enter the email used for your WebIgnitors account and we will send a secure reset link.</p>
            @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-ink w-100" type="submit">Send reset link <i class="bi bi-envelope"></i></button>
            </form>
            <a class="auth-return" href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Return to sign in</a>
        </div>
    </div>
</section>
@endsection
