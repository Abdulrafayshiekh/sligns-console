@extends('layouts.console')

@php
    $editing = $company->exists;
    $languages = \App\Support\Options::languages();
    $listValues = fn ($field) => old($field, $company->{$field} ?: ['']) ?: [''];
@endphp

@section('title', $editing ? 'Edit '.$company->name : 'Deploy a New Company')

@section('content')
    @if ($editing)
        <div class="breadcrumb-line">
            <a href="{{ route('deployer.companies.index') }}">Manage Companies</a> ›
            <a href="{{ route('deployer.companies.show', $company) }}">{{ $company->name }}</a>
        </div>
    @endif

    <div class="page-head">
        <h1>{{ $editing ? 'Edit '.$company->name : 'Deploy a New Company' }}</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2">Please check the highlighted fields.</div>
    @endif

    <form method="POST" enctype="multipart/form-data" novalidate
          action="{{ $editing ? route('deployer.companies.update', $company) : route('deployer.companies.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="eyebrow">Branding</div>

                <div class="mb-3">
                    <label class="form-label" for="console_language">Console language</label>
                    <select id="console_language" name="console_language" class="form-select @error('console_language') is-invalid @enderror">
                        @foreach ($languages as $code => $name)
                            <option value="{{ $code }}" @selected(old('console_language', $company->console_language) === $code)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('console_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="interface_color">Interface colour</label>
                    <div class="input-group">
                        <input id="interface_color" type="text" name="interface_color" value="{{ old('interface_color', $company->interface_color) }}"
                               class="form-control @error('interface_color') is-invalid @enderror" placeholder="#hex" data-colour-text="colour_picker" maxlength="7">
                        <input id="colour_picker" type="color" class="form-control form-control-color" value="{{ old('interface_color', $company->interface_color) }}" aria-label="Pick a colour">
                        @error('interface_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="logo">Logo</label>
                    <label class="logo-drop @error('logo') border-danger @enderror" id="logo_preview" for="logo">
                        @if ($company->logoUrl())
                            <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}">
                        @endif
                        <strong>Upload logo</strong>
                        <span class="text-muted small">PNG, JPG or WebP, max 250 × 120 px</span>
                    </label>
                    <input id="logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="d-none" data-logo-input="logo_preview">
                    @error('logo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="eyebrow">Console sign-in</div>
                <p class="text-muted small">The company uses this account to open its own console.</p>

                <div class="mb-3">
                    <label class="form-label" for="admin_name">Administrator name</label>
                    <input id="admin_name" name="admin_name" value="{{ old('admin_name', $admin?->name) }}" class="form-control @error('admin_name') is-invalid @enderror">
                    @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="admin_email">Sign-in email</label>
                    <input id="admin_email" type="email" name="admin_email" value="{{ old('admin_email', $admin?->email) }}" class="form-control @error('admin_email') is-invalid @enderror">
                    @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="admin_password">{{ $editing ? 'New password (leave empty to keep)' : 'Password' }}</label>
                    <input id="admin_password" type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" autocomplete="new-password">
                    @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-lg-6">
                <div class="eyebrow">Company profile</div>

                <div class="mb-3">
                    <label class="form-label" for="name">Company name</label>
                    <input id="name" name="name" value="{{ old('name', $company->name) }}" class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="contact_person">Contact person</label>
                    <input id="contact_person" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}" class="form-control @error('contact_person') is-invalid @enderror">
                    @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="preferred_language">Preferred language</label>
                    <select id="preferred_language" name="preferred_language" class="form-select @error('preferred_language') is-invalid @enderror">
                        @foreach ($languages as $code => $name)
                            <option value="{{ $code }}" @selected(old('preferred_language', $company->preferred_language) === $code)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('preferred_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="about">About the company</label>
                    <textarea id="about" name="about" rows="3" class="form-control @error('about') is-invalid @enderror">{{ old('about', $company->about) }}</textarea>
                    @error('about')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @foreach (['addresses' => ['Address', 'text'], 'phones' => ['Phone', 'tel'], 'emails' => ['Email', 'email']] as $field => [$label, $type])
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="form-label">{{ $label }}</span>
                            <button type="button" class="link-button small" data-add-more="{{ $field }}_list">Add more +</button>
                        </div>
                        <div class="add-more-list" id="{{ $field }}_list">
                            @foreach ($listValues($field) as $i => $value)
                                <div class="input-group">
                                    <input type="{{ $type }}" name="{{ $field }}[]" value="{{ $value }}" aria-label="{{ $label }}"
                                           class="form-control @error($field.'.'.$i) is-invalid @enderror">
                                    <button type="button" class="btn btn-outline-secondary" data-remove-row aria-label="Remove">×</button>
                                    @error($field.'.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label" for="city">City</label>
                        <input id="city" name="city" value="{{ old('city', $company->city) }}" class="form-control @error('city') is-invalid @enderror">
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="country">Country</label>
                        <select id="country" name="country" class="form-select @error('country') is-invalid @enderror">
                            <option value="">Choose…</option>
                            @foreach (\App\Support\Options::countries() as $country)
                                <option @selected(old('country', $company->country) === $country)>{{ $country }}</option>
                            @endforeach
                        </select>
                        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="time_zone">Time zone</label>
                        <select id="time_zone" name="time_zone" class="form-select @error('time_zone') is-invalid @enderror">
                            @foreach (\App\Support\Options::timeZones() as $zone)
                                <option @selected(old('time_zone', $company->time_zone) === $zone)>{{ $zone }}</option>
                            @endforeach
                        </select>
                        @error('time_zone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="text_opt_in" id="text_opt_in" value="1" @checked(old('text_opt_in', $company->text_opt_in))>
                            <label class="form-check-label" for="text_opt_in">Text opt-in</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 mt-2">
            <a href="{{ $editing ? route('deployer.companies.show', $company) : route('deployer.home') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-brand px-4">{{ $editing ? 'Save Company' : 'Deploy Company' }}</button>
        </div>
    </form>
@endsection
