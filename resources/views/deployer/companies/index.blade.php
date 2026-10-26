@extends('layouts.console')

@section('title', 'Manage Companies')

@section('content')
    <div class="page-head">
        <div>
            <h1>Companies</h1>
            <p>Every coaching company deployed on the Aligna Suite.</p>
        </div>
        <a href="{{ route('deployer.companies.create') }}" class="btn btn-brand px-4 py-2">+ Deploy a New Company</a>
    </div>

    @include('deployer.companies._table')
@endsection
