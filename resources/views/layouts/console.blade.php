@extends('layouts.base')

@section('body')
    <div class="shell">
        <header class="site-header">
            <a href="{{ route('home') }}" class="header-brand">
                @include('partials.logo')
            </a>

            <div class="header-tools">
                @include('partials.theme-picker')

                <div class="header-user">
                    <span class="header-user-name">{{ auth()->user()->name }}</span>
                    <span class="header-user-role">{{ __(auth()->user()->role->label()) }}</span>
                </div>

                <form method="POST" action="{{ route('sign-out') }}">
                    @csrf
                    <button type="submit" class="btn-header">{{ __('Sign out') }}</button>
                </form>
            </div>
        </header>

        <div class="console">
            <aside class="console-sidebar">
                <nav class="console-nav" aria-label="{{ __('Main') }}">
                    @if (auth()->user()->isSuperAdmin())
                        @include('partials.nav.deployer')
                    @elseif (auth()->user()->isCompany())
                        @include('partials.nav.company')
                    @else
                        @include('partials.nav.coach')
                    @endif
                </nav>
            </aside>

            <main class="console-main">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>

        @include('partials.footer')
    </div>
@endsection
