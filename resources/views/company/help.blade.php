@extends('layouts.console')

@section('title', __('Help'))

@section('content')
    <div class="page-head">
        <h1>{{ __('Help') }}</h1>
    </div>

    <div class="panel help-content">
        <h2>{{ __('Dashboard') }}</h2>
        <p>{{ __('See how many coaches you have, how many clients are assigned to them, and how many clients used one of the five apps in the last 14 days. Client details are shown only as totals.') }}</p>

        <h2>{{ __('Coaches') }}</h2>
        <p>{{ __('Add a coach with their name, email and a console password. Edit a coach to change their contact details. A coach can be removed once they have no clients.') }}</p>

        <h2>{{ __('When a coach leaves') }}</h2>
        <ol>
            <li>{{ __('Open Reassign Clients and choose the coach who is leaving.') }}</li>
            <li>{{ __('Pick the new coach and select Request consent. Each client receives an email to agree to the move.') }}</li>
            <li>{{ __('Select Reassign. Only clients who agreed are moved. Their activity history opens for the new coach; private notes stay with the coach who wrote them.') }}</li>
        </ol>

        <h2>{{ __('Need more help?') }}</h2>
        <p>{{ __('Contact the Aligna team.') }}</p>
    </div>
@endsection
