@extends('layouts.console')

@php
    $statusPill = ['active' => 'pill-green', 'idle' => 'pill-amber', 'inactive' => 'pill-red'];
@endphp

@section('title', __('Clients'))

@section('content')
    <div class="page-head align-items-center">
        <h1>{{ __('Welcome back, :name', ['name' => $coach->name]) }}</h1>
        <a href="{{ route('coach.contact-info.edit') }}" class="btn btn-soft rounded-circle px-3" title="{{ __('Edit your address, phone and email') }}" aria-label="{{ __('Edit your contact info') }}">+</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="alert-card alert-blue">
                <h3>{{ __('Birthdays this week') }}</h3>
                <ul>
                    @forelse ($birthdays->take(4) as $client)
                        <li>
                            {{ $client->name }} · {{ $client->birthday->translatedFormat('j M') }} ·
                            <a href="mailto:{{ $client->email }}?subject={{ rawurlencode(__('Happy birthday!')) }}">{{ __('Send wishes') }}</a>
                        </li>
                    @empty
                        <li class="text-muted">{{ __('No birthdays this week.') }}</li>
                    @endforelse
                    @if ($birthdays->count() > 4)
                        <li class="text-muted">{{ __('and :count more', ['count' => $birthdays->count() - 4]) }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="alert-card alert-amber">
                <h3>{{ __('Not checked in 7+ days') }}</h3>
                <ul>
                    @forelse ($unchecked->take(4) as $client)
                        <li>{{ $client->name }} · <a href="{{ route('coach.clients.show', $client) }}">{{ __('Open profile') }}</a></li>
                    @empty
                        <li class="text-muted">{{ __('You have checked on everyone this week.') }}</li>
                    @endforelse
                    @if ($unchecked->count() > 4)
                        <li class="text-muted">{{ __('and :count more', ['count' => $unchecked->count() - 4]) }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="alert-card alert-red">
                <h3>{{ __('Inactive 14+ days') }}</h3>
                <ul>
                    @forelse ($inactive->take(4) as $client)
                        <li>{{ $client->name }} · <a href="mailto:{{ $client->email }}">{{ __('Reach out') }}</a></li>
                    @empty
                        <li class="text-muted">{{ __('Everyone has been active recently.') }}</li>
                    @endforelse
                    @if ($inactive->count() > 4)
                        <li class="text-muted">{{ __('and :count more', ['count' => $inactive->count() - 4]) }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    @if ($unchecked->isNotEmpty())
        <p class="text-muted small mb-3">{{ __('You haven’t checked on :count of your clients in a while.', ['count' => $unchecked->count()]) }}</p>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <h2 class="section-title">{{ __('My Clients') }}</h2>
        <form method="GET" role="search">
            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('Search clients') }}" aria-label="{{ __('Search clients') }}">
        </form>
    </div>

    <div class="table-wrap">
        <table class="table console-table">
            <thead>
                <tr>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Last active') }}</th>
                    <th>{{ __('Apps used') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Contact') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    @php($status = $client->activityStatus())
                    <tr class="row-status status-{{ $status->value }}" data-href="{{ route('coach.clients.show', $client) }}">
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td>{{ $client->last_active_on ? \Illuminate\Support\Carbon::parse($client->last_active_on)->translatedFormat('j M Y') : __('Never') }}</td>
                        <td>{{ __(':count of 5', ['count' => $appsUsed[$client->id] ?? 0]) }}</td>
                        <td><span class="pill {{ $statusPill[$status->value] }}">{{ __($status->label()) }}</span></td>
                        <td class="actions text-nowrap">
                            <a href="mailto:{{ $client->email }}">{{ __('Email') }}</a>
                            @if ($client->phone)
                                <a href="sms:{{ $client->phone }}">{{ __('Text') }}</a>
                                <a href="tel:{{ $client->phone }}">{{ __('Call') }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            {{ $search ? __('No clients match your search.') : __('No clients yet.') }}
                            @unless ($search)
                                <a href="{{ route('coach.clients.create') }}">{{ __('Create your first client') }}</a>
                            @endunless
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="legend">
        <span class="dot" style="background: var(--green)"></span>{{ __('Active') }} ·
        <span class="dot" style="background: var(--amber)"></span>{{ __('7–13 days without activity') }} ·
        <span class="dot" style="background: var(--red)"></span>{{ __('14+ days') }}.
        {{ __('Apps used counts the last 30 days. Click a row to open the client.') }}
    </p>
@endsection
