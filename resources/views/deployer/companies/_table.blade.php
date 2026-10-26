<div class="d-flex align-items-center justify-content-between mb-3 gap-3">
    <h2 class="section-title">Manage Companies</h2>
    <form method="GET" class="ms-auto" role="search">
        <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search companies" aria-label="Search companies">
    </form>
</div>

<div class="table-wrap">
    <table class="table console-table">
        <thead>
            <tr>
                <th>Company</th>
                <th>Coaches</th>
                <th>Clients</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td class="fw-semibold">{{ $company->name }}</td>
                    <td>{{ $company->coaches_count }}</td>
                    <td>{{ $company->clients_count }}</td>
                    <td>
                        <span @class(['pill', 'pill-green' => $company->isActive(), 'pill-red' => ! $company->isActive()])>
                            {{ $company->isActive() ? 'Active' : 'Suspended' }}
                        </span>
                    </td>
                    <td class="actions text-nowrap">
                        <a href="{{ route('deployer.companies.show', $company) }}">View</a>
                        <a href="{{ route('deployer.companies.edit', $company) }}">Edit</a>
                        <form method="POST" action="{{ route('deployer.companies.status', $company) }}" class="d-inline"
                              data-confirm="{{ $company->isActive() ? 'Suspend '.$company->name.'? Its company and coach consoles will be locked.' : 'Reactivate '.$company->name.'?' }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" @class(['link-button', 'danger' => $company->isActive()])>{{ $company->isActive() ? 'Suspend' : 'Reactivate' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">No companies found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $companies->links() }}</div>
