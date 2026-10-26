<?php

namespace App\Http\Controllers\Deployer;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        return view('deployer.companies.index', [
            'companies' => Company::listing($request->query('search'))->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('deployer.companies.form', [
            'company' => new Company([
                'console_language' => 'en',
                'preferred_language' => 'en',
                'interface_color' => '#c90076',
                'time_zone' => 'UTC',
            ]),
            'admin' => null,
        ]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $company = DB::transaction(function () use ($request, $data) {
            $company = Company::create($request->companyData() + [
                'slug' => Company::uniqueSlug($data['name']),
                'logo_path' => $request->file('logo')?->store('logos', 'public'),
                'status' => Company::ACTIVE,
            ]);

            $company->users()->create([
                'role' => Role::Company,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => $data['admin_password'],
            ]);

            return $company;
        });

        return redirect()->route('deployer.companies.show', $company)->with('status', 'Company deployed.');
    }

    public function show(Company $company): View
    {
        $company->loadCount(['coaches', 'clients']);

        return view('deployer.companies.show', [
            'company' => $company,
            'admin' => $company->admins()->first(),
        ]);
    }

    public function edit(Company $company): View
    {
        return view('deployer.companies.form', [
            'company' => $company,
            'admin' => $company->admins()->first(),
        ]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $company, $data) {
            $attributes = $request->companyData();

            if ($request->hasFile('logo')) {
                if ($company->logo_path) {
                    Storage::disk('public')->delete($company->logo_path);
                }

                $attributes['logo_path'] = $request->file('logo')->store('logos', 'public');
            }

            $company->update($attributes);

            $admin = $company->admins()->firstOrNew([], ['role' => Role::Company]);
            $admin->fill(['name' => $data['admin_name'], 'email' => $data['admin_email']]);

            if (! empty($data['admin_password'])) {
                $admin->password = $data['admin_password'];
            }

            $company->users()->save($admin);
        });

        return redirect()->route('deployer.companies.show', $company)->with('status', 'Company saved.');
    }

    public function toggleStatus(Company $company): RedirectResponse
    {
        $company->update(['status' => $company->isActive() ? Company::SUSPENDED : Company::ACTIVE]);

        return back()->with('status', $company->isActive() ? 'Company reactivated.' : 'Company suspended.');
    }
}
