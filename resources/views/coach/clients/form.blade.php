@extends('layouts.console')

@use('App\Support\Options')
@php
    $editing = $client->exists;
@endphp

@section('title', $editing ? __('Edit :name', ['name' => $client->name]) : __('Create New Client'))

@section('content')
    @if ($editing)
        <div class="breadcrumb-line">
            <a href="{{ route('coach.clients.index') }}">{{ __('Clients') }}</a> ›
            <a href="{{ route('coach.clients.show', $client) }}">{{ $client->name }}</a>
        </div>
    @endif

    <div class="page-head">
        <h1>{{ $editing ? __('Edit :name', ['name' => $client->name]) : __('Create New Client') }}</h1>
    </div>

    <form method="POST" novalidate action="{{ $editing ? route('coach.clients.update', $client) : route('coach.clients.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="eyebrow">{{ __('Required') }}</div>

                <div class="mb-3">
                    <label class="form-label" for="name">{{ __('Full name') }} *</label>
                    <input id="name" name="name" value="{{ old('name', $client->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">{{ __('Email') }} *</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $client->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="birthday">{{ __('Birthday') }} *</label>
                    <input id="birthday" type="date" name="birthday" value="{{ old('birthday', $client->birthday?->toDateString()) }}" max="{{ today()->subDay()->toDateString() }}" class="form-control @error('birthday') is-invalid @enderror" required>
                    @error('birthday')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @unless ($editing)
                    <div class="notes-card h-auto">
                        <label class="form-label mb-0" for="notes"><h3 class="mb-0">{{ __('Private coach notes') }}</h3></label>
                        <p class="text-muted small mb-2">{{ __('Only you can see these. They never transfer.') }}</p>
                        <textarea id="notes" name="notes" rows="6" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endunless
            </div>

            <div class="col-lg-6">
                <div class="eyebrow">{{ __('Optional') }}</div>

                <div class="mb-3">
                    <label class="form-label" for="phone">{{ __('Phone') }}</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $client->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @foreach ([
                    'title' => [__('Title'), Options::titles()],
                    'marital_status' => [__('Marital status'), Options::maritalStatuses()],
                ] as $field => [$label, $choices])
                    <div class="mb-3">
                        <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" class="form-select @error($field) is-invalid @enderror">
                            <option value="">{{ __('Choose…') }}</option>
                            @foreach ($choices as $choice)
                                <option value="{{ $choice }}" @selected(old($field, $client->{$field}) === $choice)>{{ __($choice) }}</option>
                            @endforeach
                        </select>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach

                <div class="mb-3">
                    <label class="form-label" for="age">{{ __('Age') }}</label>
                    <input id="age" type="number" min="1" max="120" name="age" value="{{ old('age', $client->age) }}" class="form-control @error('age') is-invalid @enderror">
                    @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="occupation">{{ __('Occupation') }}</label>
                    <input id="occupation" name="occupation" value="{{ old('occupation', $client->occupation) }}" class="form-control @error('occupation') is-invalid @enderror">
                    @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="industry">{{ __('Industry') }}</label>
                    <select id="industry" name="industry" class="form-select @error('industry') is-invalid @enderror">
                        <option value="">{{ __('Choose…') }}</option>
                        @foreach (Options::industries() as $industry)
                            <option value="{{ $industry }}" @selected(old('industry', $client->industry) === $industry)>{{ __($industry) }}</option>
                        @endforeach
                    </select>
                    @error('industry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label" for="country">{{ __('Country') }}</label>
                        <select id="country" name="country" class="form-select @error('country') is-invalid @enderror">
                            <option value="">{{ __('Choose…') }}</option>
                            @foreach (Options::countries() as $country)
                                <option @selected(old('country', $client->country) === $country)>{{ $country }}</option>
                            @endforeach
                        </select>
                        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="language">{{ __('Language') }}</label>
                        <select id="language" name="language" class="form-select @error('language') is-invalid @enderror">
                            <option value="">{{ __('Choose…') }}</option>
                            @foreach (Options::languages() as $code => $name)
                                <option value="{{ $code }}" @selected(old('language', $client->language) === $code)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 mt-3">
            <a href="{{ $editing ? route('coach.clients.show', $client) : route('coach.clients.index') }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-brand px-4">{{ $editing ? __('Save Client') : __('Create Client') }}</button>
        </div>
    </form>

    @if ($editing)
        <form method="POST" action="{{ route('coach.clients.destroy', $client) }}" class="mt-4"
              data-confirm="{{ __('Remove :name? They will no longer appear in your client list.', ['name' => $client->name]) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger px-0">{{ __('Remove client') }}</button>
        </form>
    @endif
@endsection
