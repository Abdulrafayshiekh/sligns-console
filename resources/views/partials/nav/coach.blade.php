<a href="{{ route('coach.clients.index') }}" @class(['active' => request()->routeIs('coach.clients.index', 'coach.clients.show', 'coach.clients.app', 'coach.clients.edit')])>{{ __('Clients') }}</a>
<a href="{{ route('coach.clients.create') }}" @class(['active' => request()->routeIs('coach.clients.create')])>{{ __('Create New Client') }}</a>
<a href="{{ route('coach.messages.index') }}" @class(['active' => request()->routeIs('coach.messages.*')])>{{ __('Messages') }}</a>
<a href="{{ route('coach.contact-info.edit') }}" @class(['active' => request()->routeIs('coach.contact-info.*')])>{{ __('Contact Info') }}</a>
<a href="{{ route('coach.help') }}" @class(['active' => request()->routeIs('coach.help')])>{{ __('Help') }}</a>
