@extends('layouts.auth')

@section('title', 'Super Deployer sign-in')

@section('content')
    <h1 class="auth-title">Aligna Account</h1>
    <p class="auth-lead">Aligna team only</p>

    <form method="POST" action="{{ route('deployer.sign-in') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="username" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="form-label" for="master_key">Master key</label>
            <input id="master_key" type="password" name="master_key" class="form-control @error('master_key') is-invalid @enderror" autocomplete="current-password" required>
            @error('master_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-brand w-100 py-2">Sign in</button>
    </form>

    <p class="auth-note">English only</p>
@endsection
