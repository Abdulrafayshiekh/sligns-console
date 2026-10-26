<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}" data-theme="{{ $theme->value }}" data-bs-theme="{{ $theme->isDark() ? 'dark' : 'light' }}" style="{{ $companyColours }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">
    <title>@yield('title') · {{ $logoText }}</title>
    <link rel="preload" href="{{ asset('fonts/suisse-intl-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap{{ $rtl ? '.rtl' : '' }}.min.css">
    <link rel="stylesheet" href="{{ asset('css/console.css') }}">
</head>
<body>
    @yield('body')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.js"></script>
    <script src="{{ asset('js/console.js') }}"></script>
</body>
</html>
