@extends('layouts.auth')

@section('title', __('Sign in'))

@section('content')
    <h1 class="auth-title">{{ __('Coaching console') }}</h1>
    <p class="auth-lead">{{ __('For coaching companies and coaches') }}</p>

    <form method="POST" action="{{ route('sign-in') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="username" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
            <label class="form-check-label" for="remember">{{ __('Keep me signed in') }}</label>
        </div>
        <button type="submit" class="btn btn-brand w-100 py-2">{{ __('Sign in') }}</button>
    </form>
@endsection
