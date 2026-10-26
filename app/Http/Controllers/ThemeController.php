<?php

namespace App\Http\Controllers;

use App\Enums\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public const COOKIE = 'console_theme';

    public function __invoke(Request $request): RedirectResponse|Response
    {
        $theme = Theme::from($request->validate([
            'theme' => ['required', Rule::enum(Theme::class)],
        ])['theme']);

        $request->user()?->update(['theme' => $theme]);

        Cookie::queue(self::COOKIE, $theme->value, 60 * 24 * 365);

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
