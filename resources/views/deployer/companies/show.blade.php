@extends('layouts.console')

@php
    $languages = \App\Support\Options::languages();
    $list = fn ($values) => $values ? implode(', ', $values) : '—';
@endphp

@section('title', $company->name)

@section('content')
    <div class="breadcrumb-line">
        <a href="{{ route('deployer.companies.index') }}">Manage Companies</a> › {{ $company->name }}
    </div>

    <div class="page-head align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="company-logo-box">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}">
                @else
                    Company logo
                @endif
            </div>
            <div>
                <h1>{{ $company->name }}</h1>
                <span @class(['pill mt-1', 'pill-green' => $company->isActive(), 'pill-red' => ! $company->isActive()])>
                    {{ $company->isActive() ? 'Active' : 'Suspended' }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deployer.companies.edit', $company) }}" class="btn btn-outline-secondary px-4">Edit</a>
            <form method="POST" action="{{ route('deployer.companies.status', $company) }}"
                  data-confirm="{{ $company->isActive() ? 'Suspend '.$company->name.'? Its company and coach consoles will be locked.' : 'Reactivate '.$company->name.'?' }}">
                @csrf
                @method('PATCH')
                <button type="submit" @class(['btn px-4', 'btn-outline-danger' => $company->isActive(), 'btn-outline-success' => ! $company->isActive()])>
                    {{ $company->isActive() ? 'Suspend' : 'Reactivate' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="stat">
                <div class="label">Coaches</div>
                <div class="value">{{ $company->coaches_count }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat">
                <div class="label">Clients</div>
                <div class="value">{{ $company->clients_count }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat">
                <div class="label">Branding</div>
                <div class="mt-2 d-flex align-items-center gap-3">
                    <span><span class="swatch" style="background: {{ $company->interface_color }}"></span> {{ $company->interface_color }}</span>
                    <span>{{ $company->languageName() }} console</span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="eyebrow">Company profile</div>
        <dl class="profile-grid">
            <div><dt>Contact person</dt><dd>{{ $company->contact_person }}</dd></div>
            <div><dt>Time zone</dt><dd>{{ $company->time_zone }}</dd></div>
            <div><dt>Emails</dt><dd>{{ $list($company->emails) }}</dd></div>
            <div><dt>Preferred language</dt><dd>{{ $languages[$company->preferred_language] ?? $company->preferred_language }}</dd></div>
            <div><dt>Phones</dt><dd>{{ $list($company->phones) }}</dd></div>
            <div><dt>Text opt-in</dt><dd>{{ $company->text_opt_in ? 'Yes' : 'No' }}</dd></div>
            <div><dt>Address</dt><dd>{{ $list($company->addresses) }}</dd></div>
            <div><dt>Console sign-in</dt><dd>{{ $admin?->email ?? '—' }}</dd></div>
            <div><dt>City, country</dt><dd>{{ collect([$company->city, $company->country])->filter()->implode(', ') ?: '—' }}</dd></div>
            <div><dt>About</dt><dd>{{ $company->about ?: '—' }}</dd></div>
        </dl>
    </div>

    <p class="privacy-note">Client names and activity are never shown at this level.</p>
@endsection
