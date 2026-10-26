@extends('layouts.console')

@php
    $panelCoach = $editing;
    $showPanel = $editing || $adding;
@endphp

@section('title', __('Coaches'))

@section('content')
    <div class="row g-4">
        <div class="{{ $showPanel ? 'col-xl-8' : 'col-12' }}">
            <div class="page-head align-items-center">
                <h1>{{ __('Coaches') }}</h1>
                <a href="{{ route('company.coaches.index', ['add' => 1]) }}" class="btn btn-brand px-4">+ {{ __('Add Coach') }}</a>
            </div>

            <div class="table-wrap">
                <table class="table console-table">
                    <thead>
                        <tr>
                            <th>{{ __('Coach') }}</th>
                            <th>{{ __('Clients') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coaches as $coach)
                            <tr @class(['table-active' => $editing?->id === $coach->id])>
                                <td class="fw-semibold">{{ $coach->name }}</td>
                                <td>{{ $coach->clients_count }}</td>
                                <td class="actions text-nowrap">
                                    <a href="{{ route('company.coaches.index', ['coach' => $coach->id]) }}">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('company.coaches.destroy', $coach) }}" class="d-inline"
                                          data-confirm="{{ __('Remove :name from your coaches?', ['name' => $coach->name]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-button">{{ __('Remove') }}</button>
                                    </form>
                                    <a href="{{ route('company.reassign.show', $coach) }}">{{ __('Reassign') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">{{ __('No coaches yet. Add your first coach.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="privacy-note">{{ __('Coaches’ clients are not listed here.') }}</p>
        </div>

        @if ($showPanel)
            <div class="col-xl-4">
                <div class="side-panel">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h2 class="section-title">{{ $panelCoach ? __('Edit coach: :name', ['name' => $panelCoach->name]) : __('Add coach') }}</h2>
                        <a href="{{ route('company.coaches.index') }}" class="text-muted text-decoration-none" aria-label="{{ __('Close') }}">×</a>
                    </div>

                    <form method="POST" novalidate
                          action="{{ $panelCoach ? route('company.coaches.update', $panelCoach) : route('company.coaches.store') }}">
                        @csrf
                        @if ($panelCoach)
                            @method('PUT')
                        @else
                            <input type="hidden" name="panel" value="add">
                        @endif

                        <div class="mb-2">
                            <label class="form-label" for="name">{{ __('Full name') }}</label>
                            <input id="name" name="name" value="{{ old('name', $panelCoach?->name) }}" class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="email">{{ __('Email') }}</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $panelCoach?->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="phone">{{ __('Phone') }}</label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone', $panelCoach?->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="address">{{ __('Address') }}</label>
                            <input id="address" name="address" value="{{ old('address', $panelCoach?->address) }}" class="form-control @error('address') is-invalid @enderror">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="country">{{ __('Country') }}</label>
                            <select id="country" name="country" class="form-select @error('country') is-invalid @enderror">
                                <option value="">{{ __('Choose…') }}</option>
                                @foreach (\App\Support\Options::countries() as $country)
                                    <option @selected(old('country', $panelCoach?->country) === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">{{ $panelCoach ? __('New password (leave empty to keep)') : __('Console password') }}</label>
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-brand w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
