@extends('layouts.console')

@use('App\Enums\SuiteApp')

@section('title', $client->name.' · '.$tab->label())

@section('content')
    @include('coach.clients._header')

    <div class="panel mb-3">
        <h3 class="section-title">{{ __($tab->chartTitle()) }}</h3>
        <p class="text-muted small">{{ __('From :app in :range.', ['app' => $tab->label(), 'range' => mb_strtolower($range->label())]) }}</p>
        <div class="chart-box tall">
            @if ($chart['empty'])
                <div class="empty-state">{{ __('No :app activity in this range.', ['app' => $tab->label()]) }}</div>
            @else
                <canvas data-chart='@json($chart)' aria-label="{{ __($tab->chartTitle()) }}"></canvas>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-3 align-items-start">
        @foreach ($summary as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="stat">
                    <div class="label">{{ $label }}</div>
                    <div class="value">{{ $value }}</div>
                </div>
            </div>
        @endforeach
        <div class="col-md-6 col-xl-3">
            @include('coach.clients._notes')
        </div>
    </div>

    <div class="panel">
        @switch ($tab)
            @case (SuiteApp::Scintilla)
                <div class="eyebrow">{{ __('Subjects chosen') }}</div>
                <table class="table console-table">
                    <thead><tr><th>{{ __('Subject') }}</th><th>{{ __('Times chosen') }}</th><th>{{ __('Messages opened') }}</th></tr></thead>
                    <tbody>
                        @forelse ($details as $row)
                            <tr><td>{{ $row->name }}</td><td>{{ $row->times }}</td><td>{{ $row->opened }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">{{ __('No Scintilla activity in this range.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @break

            @case (SuiteApp::TargetPro)
                <div class="eyebrow">{{ __('Targets and goals') }}</div>
                @forelse ($details as $row)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between"><span>{{ $row->name }}</span><span class="fw-semibold">{{ $row->progress }}%</span></div>
                        <div class="progress progress-thin" role="progressbar" aria-valuenow="{{ $row->progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $row->name }}">
                            <div class="progress-bar" style="width: {{ $row->progress }}%; background: var(--brand)"></div>
                        </div>
                        <div class="text-muted small">{{ __('Updated :date', ['date' => $row->updated->translatedFormat('j M Y')]) }}</div>
                    </div>
                @empty
                    <div class="empty-state">{{ __('No TargetPro activity in this range.') }}</div>
                @endforelse
                @break

            @case (SuiteApp::Momentum)
                <div class="eyebrow">{{ __('Recent check-ins') }}</div>
                <table class="table console-table">
                    <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Emotional status') }}</th></tr></thead>
                    <tbody>
                        @forelse ($details as $row)
                            <tr><td>{{ $row->activity_date->translatedFormat('j M Y') }}</td><td>{{ rtrim(rtrim(number_format($row->value, 1), '0'), '.') }} / 10</td></tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">{{ __('No Momentum check-ins in this range.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @break

            @case (SuiteApp::Lotus)
                <div class="eyebrow">{{ __('Recent sessions') }}</div>
                <table class="table console-table">
                    <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Sessions') }}</th><th>{{ __('Minutes') }}</th></tr></thead>
                    <tbody>
                        @forelse ($details as $row)
                            <tr><td>{{ $row->activity_date->translatedFormat('j M Y') }}</td><td>{{ $row->quantity }}</td><td>{{ round($row->value) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">{{ __('No Lotus sessions in this range.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <p class="privacy-note mb-0">{{ __('Lotus shows sessions and time only, never transcripts.') }}</p>
                @break

            @case (SuiteApp::ScriptBuilder)
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="eyebrow">{{ __('Areas') }}</div>
                        <table class="table console-table">
                            <thead><tr><th>{{ __('Area') }}</th><th>{{ __('Scripts') }}</th></tr></thead>
                            <tbody>
                                @forelse ($details as $row)
                                    <tr><td>{{ $row->name }}</td><td>{{ $row->scripts }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="empty-state">{{ __('No scripts in this range.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-7">
                        <div class="eyebrow">{{ __('Scripts the client shared') }}</div>
                        @forelse ($sharedScripts as $script)
                            <div class="note-item">
                                <time datetime="{{ $script->activity_date->toDateString() }}">{{ $script->activity_date->translatedFormat('j M Y') }} · {{ $script->label }}</time>
                                {{ $script->content }}
                            </div>
                        @empty
                            <p class="text-muted">{{ __('The client has not shared any scripts in this range.') }}</p>
                        @endforelse
                    </div>
                </div>
                <p class="privacy-note mb-0">{{ __('Script content appears only if the client shares it.') }}</p>
                @break
        @endswitch
    </div>
@endsection
