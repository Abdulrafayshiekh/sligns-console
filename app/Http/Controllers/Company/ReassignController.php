<?php

namespace App\Http\Controllers\Company;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Mail\ConsentRequestMail;
use App\Models\Reassignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReassignController extends Controller
{
    public function index(Request $request): View
    {
        return view('company.reassign.index', [
            'coaches' => $request->user()->company->coaches()->withCount('clients')->orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, User $coach): View
    {
        Gate::authorize('manage-coach', $coach);

        $clients = $coach->clients()->orderBy('name')->get(['id', 'name', 'email', 'phone', 'coach_id']);

        return view('company.reassign.show', [
            'coach' => $coach,
            'clients' => $clients,
            'consents' => $this->openConsents($coach, $clients->pluck('id')),
            'otherCoaches' => $request->user()->company->coaches()->whereKeyNot($coach->id)->orderBy('name')->get(),
        ]);
    }

    public function requestConsent(Request $request, User $coach): RedirectResponse
    {
        Gate::authorize('manage-coach', $coach);

        $newCoachId = $this->validatedNewCoach($request, $coach);
        $consents = $this->openConsents($coach, $coach->clients()->pluck('id'));
        $sent = 0;

        foreach ($coach->clients as $client) {
            $consent = $consents->get($client->id);

            if ($consent?->status === Reassignment::GIVEN) {
                continue;
            }

            $consent ??= new Reassignment([
                'company_id' => $coach->company_id,
                'client_id' => $client->id,
                'from_coach_id' => $coach->id,
                'token' => Str::random(48),
                'status' => Reassignment::PENDING,
            ]);

            $consent->to_coach_id = $newCoachId;
            $consent->save();

            Mail::to($client->email)->send(new ConsentRequestMail($consent->load('company', 'client', 'fromCoach', 'toCoach')));
            $sent++;
        }

        return redirect()
            ->route('company.reassign.show', ['coach' => $coach, 'to' => $newCoachId])
            ->with('status', trans_choice(':count consent request sent.|:count consent requests sent.', $sent));
    }

    public function store(Request $request, User $coach): RedirectResponse
    {
        Gate::authorize('manage-coach', $coach);

        $newCoachId = $this->validatedNewCoach($request, $coach);
        $given = $this->openConsents($coach, $coach->clients()->pluck('id'))
            ->where('status', Reassignment::GIVEN);

        DB::transaction(function () use ($given, $newCoachId) {
            foreach ($given as $consent) {
                $consent->client->update(['coach_id' => $newCoachId, 'coach_viewed_at' => null]);
                $consent->update([
                    'status' => Reassignment::COMPLETED,
                    'to_coach_id' => $newCoachId,
                    'completed_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('company.reassign.show', ['coach' => $coach, 'to' => $newCoachId])
            ->with('status', trans_choice(':count client moved.|:count clients moved.', $given->count()));
    }

    private function openConsents(User $coach, $clientIds)
    {
        return Reassignment::with('client')
            ->where('from_coach_id', $coach->id)
            ->whereIn('client_id', $clientIds)
            ->whereIn('status', [Reassignment::PENDING, Reassignment::GIVEN])
            ->get()
            ->keyBy('client_id');
    }

    private function validatedNewCoach(Request $request, User $coach): int
    {
        $data = $request->validate([
            'to_coach_id' => [
                'required',
                Rule::notIn([$coach->id]),
                Rule::exists('users', 'id')
                    ->where('company_id', $coach->company_id)
                    ->where('role', Role::Coach->value)
                    ->whereNull('deleted_at'),
            ],
        ], [], ['to_coach_id' => __('new coach')]);

        return (int) $data['to_coach_id'];
    }
}
