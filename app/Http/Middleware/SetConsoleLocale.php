<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetConsoleLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($company = $request->user()?->company) {
            App::setLocale($company->console_language);
        }

        return $next($request);
    }
}
