@extends('layouts.console')

@php
    $sortLabels = [
        'clients' => __('client count'),
        'country' => __('country'),
        'language' => __('language'),
        'age' => __('age'),
    ];
    $groupLabels = ['country' => __('Country'), 'language' => __('Language'), 'age' => __('Age')];
@endphp

@section('title', __('Dashboard'))

@section('content')
    <div class="page-head align-items-center">
        <h1>{{ __('Welcome, :name', ['name' => $company->name]) }}</h1>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="sort" class="text-muted text-nowrap">{{ __('Sort by') }}</label>
            <select id="sort" name="sort" class="form-select" onchange="this.form.submit()">
                @foreach ($sortLabels as $value => $label)
                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="stat">
                <div class="label">{{ __('Total coaches') }}</div>
                <div class="value">{{ $totals['coaches'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat">
                <div class="label">{{ __('Active clients') }}</div>
                <div class="value">{{ $totals['clients'] }}</div>
                <div class="hint">{{ __('Assigned to a coach') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat">
                <div class="label">{{ __('Clients using the Suite') }}</div>
                <div class="value">{{ $totals['using'] }}</div>
                <div class="hint">{{ __('Used an app in the last 14 days') }}</div>
            </div>
        </div>
    </div>

    <div class="panel mb-3">
        <div class="eyebrow">{{ __('Monthly activity') }}</div>
        <div class="chart-box" style="height: 220px">
            <canvas data-chart='@json($chart)' aria-label="{{ __('Monthly activity') }}"></canvas>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="table-wrap">
                <table class="table console-table">
                    <thead>
                        <tr>
                            <th>{{ __('Coach') }}</th>
                            <th>{{ __('Clients') }}</th>
                            <th>{{ __('Active in Suite') }}</th>
                            <th>{{ __('Country') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coaches as $row)
                            <tr>
                                <td class="fw-semibold">{{ $row->coach->name }}</td>
                                <td>{{ $row->clients }}</td>
                                <td>{{ $row->active }}</td>
                                <td>{{ $row->coach->country ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-state">{{ __('No coaches yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="privacy-note">{{ __('Client demographics shown in aggregate only') }}</p>
        </div>

        <div class="col-xl-4">
            <div class="panel">
                <div class="eyebrow">{{ __('Clients by :group', ['group' => mb_strtolower($groupLabels[$group])]) }}</div>
                @forelse ($demographics as $label => $count)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span>{{ $label }}</span>
                        <span class="fw-semibold">{{ $count }}</span>
                    </div>
                @empty
                    <div class="empty-state">{{ __('No clients yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
