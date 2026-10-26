<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DeployerSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.deployer-sign-in');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'master_key' => ['required', 'string'],
        ]);

        $signedIn = Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['master_key'],
            'role' => Role::SuperAdmin->value,
        ]);

        if (! $signedIn) {
            throw ValidationException::withMessages([
                'email' => 'The email or master key is not correct.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('deployer.home')->with('signed_in', true);
    }
}
