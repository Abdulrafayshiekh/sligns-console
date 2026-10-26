<a href="{{ route('company.dashboard') }}" @class(['active' => request()->routeIs('company.dashboard')])>{{ __('Dashboard') }}</a>
<a href="{{ route('company.coaches.index') }}" @class(['active' => request()->routeIs('company.coaches.*')])>{{ __('Coaches') }}</a>
<a href="{{ route('company.reassign.index') }}" @class(['active' => request()->routeIs('company.reassign.*')])>{{ __('Reassign Clients') }}</a>
<a href="{{ route('company.help') }}" @class(['active' => request()->routeIs('company.help')])>{{ __('Help') }}</a>
