<?php

namespace App\Http\Controllers;

use App\Models\Reassignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public function show(Reassignment $reassignment): View
    {
        $reassignment->load('company', 'client', 'fromCoach', 'toCoach');

        return view('consent.show', ['reassignment' => $reassignment]);
    }

    public function store(Reassignment $reassignment): RedirectResponse
    {
        if ($reassignment->status === Reassignment::PENDING) {
            $reassignment->update([
                'status' => Reassignment::GIVEN,
                'consented_at' => now(),
            ]);
        }

        return redirect()->to($reassignment->consentUrl());
    }
}
