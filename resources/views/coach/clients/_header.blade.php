@use('App\Enums\SuiteApp')
@php
    $routeFor = fn (?SuiteApp $app) => $app
        ? route('coach.clients.app', ['client' => $client, 'app' => $app->slug()] + $range->query())
        : route('coach.clients.show', ['client' => $client] + $range->query());
@endphp

<div class="breadcrumb-line">
    <a href="{{ route('coach.clients.index') }}">{{ __('Clients') }}</a> › {{ $client->name }}
</div>

<div class="page-head align-items-center mb-0">
    <div>
        <h1>{{ $client->name }}</h1>
        <a href="{{ route('coach.clients.edit', $client) }}" class="small">{{ __('Edit profile') }}</a>
    </div>

    <form method="GET" class="range-form">
        <select name="range" class="form-select" aria-label="{{ __('Date range') }}">
            @foreach (\App\Support\DateRange::PRESETS as $days)
                <option value="{{ $days }}" @selected($range->preset === $days)>{{ __('Past :days days', ['days' => $days]) }}</option>
            @endforeach
            <option value="" @selected(! $range->preset)>{{ __('Custom') }}</option>
        </select>
        <input type="date" name="from" value="{{ $range->from->toDateString() }}" class="form-control" aria-label="{{ __('From') }}">
        <span class="text-muted">–</span>
        <input type="date" name="to" value="{{ $range->to->toDateString() }}" class="form-control" aria-label="{{ __('To') }}">
        <button type="submit" class="btn btn-outline-secondary">{{ __('Apply') }}</button>
    </form>

    <div class="btn-group contact-group" role="group" aria-label="{{ __('Contact') }}">
        <a href="mailto:{{ $client->email }}" class="btn btn-soft">{{ __('Email') }}</a>
        @if ($client->phone)
            <a href="sms:{{ $client->phone }}" class="btn btn-soft">{{ __('Text') }}</a>
            <a href="tel:{{ $client->phone }}" class="btn btn-soft">{{ __('Call') }}</a>
        @endif
    </div>
</div>

<nav class="tabs-line" aria-label="{{ __('Apps') }}">
    <a href="{{ $routeFor(null) }}" @class(['active' => $tab === null])>{{ __('Overview') }}</a>
    @foreach (SuiteApp::cases() as $app)
        <a href="{{ $routeFor($app) }}" @class(['active' => $tab === $app])>{{ $app->label() }}</a>
    @endforeach
</nav>
