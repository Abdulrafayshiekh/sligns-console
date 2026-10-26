@extends('layouts.console')

@php
    $selected = (int) old('to_coach_id', request('to'));
@endphp

@section('title', __('Reassign clients of :name', ['name' => $coach->name]))

@section('content')
    <div class="breadcrumb-line">
        <a href="{{ route('company.reassign.index') }}">{{ __('Reassign Clients') }}</a> › {{ $coach->name }}
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="page-head">
                <h1>{{ __('Reassign clients of :name (leaving)', ['name' => $coach->name]) }}</h1>
            </div>

            <div class="table-wrap">
                <table class="table console-table">
                    <thead>
                        <tr>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Email / phone') }}</th>
                            <th>{{ __('Consent') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            @php($consent = $consents->get($client->id))
                            <tr>
                                <td class="fw-semibold">{{ $client->name }}</td>
                                <td>{{ $client->email }}@if ($client->phone)<br><span class="text-muted">{{ $client->phone }}</span>@endif</td>
                                <td>
                                    @if ($consent?->status === \App\Models\Reassignment::GIVEN)
                                        <span class="pill pill-green">{{ __('Given') }}</span>
                                    @elseif ($consent)
                                        <span class="pill pill-amber">{{ __('Pending') }}</span>
                                        @if (config('app.debug'))
                                            <a href="{{ $consent->consentUrl() }}" target="_blank" class="small ms-2">{{ __('Test link') }}</a>
                                        @endif
                                    @else
                                        <span class="pill pill-gray">{{ __('Not asked') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">{{ __('This coach has no clients. You can remove the coach now.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="privacy-note">
                {{ __('Activity history unlocks only for the new coach.') }}<br>
                {{ __('Private notes never transfer.') }}
            </p>
        </div>

        <div class="col-xl-4">
            <div class="side-panel">
                <form method="POST" action="{{ route('company.reassign.store', $coach) }}" id="reassign-form" novalidate>
                    @csrf
                    <label class="form-label" for="to_coach_id">{{ __('Assign to') }}</label>
                    <select id="to_coach_id" name="to_coach_id" class="form-select mb-3 @error('to_coach_id') is-invalid @enderror">
                        <option value="">{{ __('Choose a coach…') }}</option>
                        @foreach ($otherCoaches as $other)
                            <option value="{{ $other->id }}" @selected($selected === $other->id)>{{ $other->name }}</option>
                        @endforeach
                    </select>
                    @error('to_coach_id')<div class="invalid-feedback mb-2">{{ $message }}</div>@enderror

                    <button type="submit" formaction="{{ route('company.reassign.consent', $coach) }}" class="btn btn-outline-secondary w-100 mb-2" @disabled($clients->isEmpty())>
                        {{ __('Request consent') }}
                    </button>
                    <button type="submit" class="btn btn-brand w-100" @disabled($clients->isEmpty())>{{ __('Reassign') }}</button>
                </form>
                <p class="text-muted small mt-3 mb-0">{{ __('Only clients who gave consent are moved.') }}</p>
            </div>
        </div>
    </div>
@endsection
