<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $coach = $request->user();

        return view('coach.messages', [
            'clients' => $coach->clients()->orderBy('name')->get(['id', 'name', 'email', 'phone']),
            'messages' => $coach->sentMessages()->with('client')->latest()->paginate(15),
            'selected' => (int) $request->query('client'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $coach = $request->user();

        $data = $request->validate([
            'client_id' => ['required', Rule::exists('users', 'id')->where('coach_id', $coach->id)->whereNull('deleted_at')],
            'channel' => ['required', Rule::in(['email', 'text'])],
            'subject' => ['nullable', 'required_if:channel,email', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ], [], ['client_id' => __('client'), 'body' => __('message')]);

        $client = User::findOrFail($data['client_id']);

        $coach->sentMessages()->create($data);

        if ($data['channel'] === 'email') {
            Mail::raw($data['body'], fn ($mail) => $mail
                ->to($client->email, $client->name)
                ->replyTo($coach->email, $coach->name)
                ->subject($data['subject']));

            return back()->with('status', __('Email sent to :name.', ['name' => $client->name]));
        }

        return back()->with('status', __('Text saved for :name.', ['name' => $client->name]));
    }
}
