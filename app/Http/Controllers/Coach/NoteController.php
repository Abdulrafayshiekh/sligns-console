<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NoteController extends Controller
{
    public function store(Request $request, User $client): RedirectResponse
    {
        Gate::authorize('manage-client', $client);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'noted_on' => ['nullable', 'date', 'before_or_equal:today'],
        ], [], ['body' => __('note')]);

        $request->user()->authoredNotes()->create([
            'client_id' => $client->id,
            'noted_on' => $data['noted_on'] ?? today(),
            'body' => $data['body'],
        ]);

        return back()->with('status', __('Note added.'));
    }
}
