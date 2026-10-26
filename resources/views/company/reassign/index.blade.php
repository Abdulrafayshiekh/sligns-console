@extends('layouts.console')

@section('title', __('Reassign Clients'))

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('Reassign clients') }}</h1>
            <p>{{ __('Choose the coach who is leaving. Their clients are asked for consent before they move.') }}</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table console-table">
            <thead>
                <tr>
                    <th>{{ __('Coach') }}</th>
                    <th>{{ __('Clients') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coaches as $coach)
                    <tr>
                        <td class="fw-semibold">{{ $coach->name }}</td>
                        <td>{{ $coach->clients_count }}</td>
                        <td class="text-end"><a href="{{ route('company.reassign.show', $coach) }}">{{ __('Reassign clients') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">{{ __('No coaches yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
