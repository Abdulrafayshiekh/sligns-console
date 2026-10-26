@extends('layouts.base')

@section('body')
    <div class="shell">
        <header class="site-header">
            <span class="header-brand">
                @include('partials.logo')
            </span>

            <div class="header-tools">
                @include('partials.theme-picker')
            </div>
        </header>

        <main class="auth-page">
            <div class="auth-card">
                @yield('content')
            </div>
        </main>

        @include('partials.footer')
    </div>
@endsection
