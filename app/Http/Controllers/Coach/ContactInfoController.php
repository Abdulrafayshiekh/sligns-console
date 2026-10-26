<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Support\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactInfoController extends Controller
{
    public function edit(Request $request): View
    {
        return view('coach.contact-info', ['coach' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $coach = $request->user();

        $coach->update($request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($coach->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', Rule::in(Options::countries())],
        ]));

        return back()->with('status', __('Contact info saved.'));
    }
}
