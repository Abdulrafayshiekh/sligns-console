<?php

namespace App\Providers;

use App\Enums\Theme;
use App\Http\Controllers\ThemeController;
use App\Models\User;
use App\Support\BrandColour;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::define('manage-client', fn (User $user, User $client) => $user->isCoach()
            && $client->isClient()
            && $client->coach_id === $user->id);

        View::composer('layouts.*', function ($view) {
            $data = $view->getData();
            $user = auth()->user();
            $company = $data['brandCompany'] ?? $user?->company;
            $alignaLogo = is_file(public_path('images/aligna-logo.png')) ? asset('images/aligna-logo.png') : null;

            $view->with([
                'theme' => Theme::resolve($user?->theme ?? request()->cookie(ThemeController::COOKIE), $company),
                'themes' => Theme::options($company),
                'companyColours' => BrandColour::cssVariables($company?->interface_color),
                'rtl' => $data['rtl'] ?? ($company?->isRtl() ?? false),
                'logoUrl' => $data['logoUrl'] ?? ($company ? $company->logoUrl() : $alignaLogo),
                'logoText' => $data['logoText'] ?? ($company?->name ?? 'Aligna'),
            ]);
        });

        Gate::define('manage-coach', fn (User $user, User $coach) => $user->isCompany()
            && $coach->isCoach()
            && $coach->company_id === $user->company_id);
    }
}
