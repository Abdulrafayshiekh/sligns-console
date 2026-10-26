@extends('layouts.console')

@section('title', __('Messages'))

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('Messages') }}</h1>
            <p>{{ __('Send an email or save a text for one of your clients.') }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="panel">
                <div class="eyebrow">{{ __('New message') }}</div>
                <form method="POST" action="{{ route('coach.messages.store') }}" data-channel-form novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="client_id">{{ __('Client') }}</label>
                        <select id="client_id" name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                            <option value="">{{ __('Choose a client…') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected((int) old('client_id', $selected) === $client->id)>{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block">{{ __('Send as') }}</span>
                        @foreach (['email' => __('Email'), 'text' => __('Text')] as $value => $label)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="channel" id="channel_{{ $value }}" value="{{ $value }}" @checked(old('channel', 'email') === $value)>
                                <label class="form-check-label" for="channel_{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-3" data-subject>
                        <label class="form-label" for="subject">{{ __('Subject') }}</label>
                        <input id="subject" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="body">{{ __('Message') }}</label>
                        <textarea id="body" name="body" rows="5" class="form-control @error('body') is-invalid @enderror">{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-brand px-4">{{ __('Send') }}</button>
                </form>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="table-wrap">
                <table class="table console-table">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Message') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $message)
                            <tr>
                                <td class="text-nowrap">{{ $message->created_at->translatedFormat('j M Y') }}</td>
                                <td>{{ $message->client?->name ?? __('Removed client') }}</td>
                                <td>{{ $message->channel === 'email' ? __('Email') : __('Text') }}</td>
                                <td>
                                    @if ($message->subject)<div class="fw-semibold">{{ $message->subject }}</div>@endif
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($message->body, 120) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-state">{{ __('No messages yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $messages->links() }}</div>
        </div>
    </div>
@endsection
