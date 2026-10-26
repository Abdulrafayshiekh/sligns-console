<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsoleSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.sign-in');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->password || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => __('The email or password is not correct.')]);
        }

        if ($user->isClient()) {
            throw ValidationException::withMessages([
                'email' => __('Clients use the Aligna apps. The console is for coaching companies and coaches.'),
            ]);
        }

        if ($user->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'email' => __('Aligna staff sign in on the deployer page.'),
            ]);
        }

        if (! $user->company?->isActive()) {
            throw ValidationException::withMessages([
                'email' => __('This company account is suspended. Please contact Aligna.'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->to($user->homeRoute())->with('signed_in', true);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $toDeployer = $request->user()->isSuperAdmin();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($toDeployer ? 'deployer.sign-in' : 'sign-in');
    }
}
