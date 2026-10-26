@extends('layouts.console')

@section('title', __('Help'))

@section('content')
    <div class="page-head">
        <h1>{{ __('Help') }}</h1>
    </div>

    <div class="panel help-content">
        <h2>{{ __('Your clients') }}</h2>
        <p>{{ __('Each row is coloured by activity: green means active, amber means 7 to 13 days without activity, red means 14 days or more. Click a row to open the client.') }}</p>

        <h2>{{ __('Alerts') }}</h2>
        <ul>
            <li>{{ __('Birthdays this week lists clients with a birthday in the next 7 days.') }}</li>
            <li>{{ __('Not checked in 7+ days lists clients whose profile you haven’t opened for a week.') }}</li>
            <li>{{ __('Inactive 14+ days lists clients who haven’t used any app for two weeks.') }}</li>
        </ul>

        <h2>{{ __('Client details') }}</h2>
        <p>{{ __('Choose a date range, then look at the overview or open one app’s tab. Lotus shows sessions and time only. Script Builder shows script content only when the client shares it.') }}</p>

        <h2>{{ __('Private notes') }}</h2>
        <p>{{ __('Notes are dated and visible only to you. They never move to another coach.') }}</p>
    </div>
@endsection
