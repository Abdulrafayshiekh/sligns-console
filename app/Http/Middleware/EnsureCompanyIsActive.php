<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $company = $request->user()?->company;

        if (! $company || ! $company->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('sign-in')->withErrors([
                'email' => __('This company account is suspended. Please contact Aligna.'),
            ]);
        }

        return $next($request);
    }
}
