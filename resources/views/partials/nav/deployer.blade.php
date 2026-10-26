<a href="{{ route('deployer.home') }}" @class(['active' => request()->routeIs('deployer.home')])>Home</a>
<a href="{{ route('deployer.companies.create') }}" @class(['active' => request()->routeIs('deployer.companies.create')])>Deploy Company</a>
<a href="{{ route('deployer.companies.index') }}" @class(['active' => request()->routeIs('deployer.companies.index', 'deployer.companies.show', 'deployer.companies.edit')])>Manage Companies</a>
