@extends('layouts.console')

@section('title', __('Contact Info'))

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('Contact info') }}</h1>
            <p>{{ __('Changes are shared with your company and with Aligna.') }}</p>
        </div>
    </div>

    <div class="panel" style="max-width: 640px">
        <form method="POST" action="{{ route('coach.contact-info.update') }}" novalidate>
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ __('Name') }}</label>
                <input value="{{ $coach->name }}" class="form-control" disabled>
                <div class="form-text">{{ __('Your company can change your name.') }}</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email', $coach->email) }}" class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="phone">{{ __('Phone') }}</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone', $coach->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="address">{{ __('Address') }}</label>
                <input id="address" name="address" value="{{ old('address', $coach->address) }}" class="form-control @error('address') is-invalid @enderror">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label" for="country">{{ __('Country') }}</label>
                <select id="country" name="country" class="form-select @error('country') is-invalid @enderror">
                    <option value="">{{ __('Choose…') }}</option>
                    @foreach (\App\Support\Options::countries() as $country)
                        <option @selected(old('country', $coach->country) === $country)>{{ $country }}</option>
                    @endforeach
                </select>
                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-brand px-4">{{ __('Save') }}</button>
        </form>
    </div>
@endsection
