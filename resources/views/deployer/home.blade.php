@extends('layouts.console')

@section('title', 'Home')

@section('content')
    <div class="page-head">
        <div>
            <h1>Welcome back, Aligna team</h1>
            <p>Deploy a new coaching company or manage existing ones.</p>
        </div>
        <a href="{{ route('deployer.companies.create') }}" class="btn btn-brand px-4 py-2">+ Deploy a New Company</a>
    </div>

    @include('deployer.companies._table')
@endsection
