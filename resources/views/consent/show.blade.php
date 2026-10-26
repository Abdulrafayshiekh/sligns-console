@extends('layouts.auth', [
    'brandCompany' => $reassignment->company,
    'rtl' => in_array($reassignment->client->language, \App\Support\Options::RTL_LANGUAGES, true),
])

@section('title', 'Coach change')

@section('content')
    @if ($reassignment->status === \App\Models\Reassignment::PENDING)
        <h1 class="h5 fw-bold mb-3">Hello {{ $reassignment->client->name }},</h1>
        <p>{{ $reassignment->fromCoach->name }} is leaving {{ $reassignment->company->name }}.</p>
        <p>Do you agree to be moved to {{ $reassignment->toCoach?->name ?? 'a new coach' }}? Your new coach will be able to see your app activity. Private notes from your previous coach are never shared.</p>
        <form method="POST" action="{{ URL::temporarySignedRoute('consent.store', now()->addHour(), ['reassignment' => $reassignment->token]) }}">
            @csrf
            <button type="submit" class="btn btn-brand w-100 py-2">I agree</button>
        </form>
        <p class="text-muted small mt-3 mb-0">If you don’t agree, you can close this page. Nothing changes until you agree.</p>
    @else
        <h1 class="h5 fw-bold mb-3">Thank you, {{ $reassignment->client->name }}.</h1>
        <p class="mb-0">Your consent has been recorded. {{ $reassignment->company->name }} will let you know when your new coach is ready.</p>
    @endif
@endsection
