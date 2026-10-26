@extends('layouts.console')

@use('App\Enums\SuiteApp')

@section('title', $client->name)

@section('content')
    @include('coach.clients._header')

    <div class="row g-3">
        @foreach (SuiteApp::cases() as $app)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('coach.clients.app', ['client' => $client, 'app' => $app->slug()] + $range->query()) }}" class="text-decoration-none text-reset">
                    <div class="chart-card">
                        <h3>{{ $app->label() }}: {{ mb_strtolower(__($app->chartTitle())) }}</h3>
                        <div class="chart-box">
                            @if ($charts[$app->value]['empty'])
                                <div class="empty-state">{{ __('No activity in this range') }}</div>
                            @else
                                <canvas data-chart='@json($charts[$app->value])' data-size="small" aria-label="{{ $app->label() }}"></canvas>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

        <div class="col-md-6 col-xl-4">
            @include('coach.clients._notes')
        </div>
    </div>

    <p class="privacy-note">{{ __('Lotus and Script Builder show usage only; script content appears only if the client shares it.') }}</p>
@endsection
