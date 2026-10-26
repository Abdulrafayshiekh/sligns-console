<?php

namespace App\Http\Controllers\Coach;

use App\Enums\ActivityStatus;
use App\Enums\Role;
use App\Enums\SuiteApp;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\AppActivity;
use App\Models\User;
use App\Support\ClientActivity;
use App\Support\DateRange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $coach = $request->user();
        $search = trim((string) $request->query('search'));

        $all = $coach->clients()
            ->withMax('activities as last_active_on', 'activity_date')
            ->orderBy('name')
            ->get();

        $clients = $search === ''
            ? $all
            : $all->filter(fn (User $client) => str_contains(mb_strtolower($client->name.' '.$client->email), mb_strtolower($search)))->values();

        $appsUsed = AppActivity::whereIn('client_id', $all->pluck('id'))
            ->where('activity_date', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('client_id, count(distinct app) as apps')
            ->groupBy('client_id')
            ->pluck('apps', 'client_id');

        return view('coach.clients.index', [
            'coach' => $coach,
            'clients' => $clients,
            'appsUsed' => $appsUsed,
            'search' => $search,
            'birthdays' => $all->filter(fn (User $client) => $this->birthdayThisWeek($client->birthday))
                ->sortBy(fn (User $client) => $this->nextBirthday($client->birthday))
                ->values(),
            'unchecked' => $all->filter(fn (User $client) => ! $client->coach_viewed_at || $client->coach_viewed_at->lt(now()->subDays(7)))->values(),
            'inactive' => $all->filter(fn (User $client) => $client->activityStatus() === ActivityStatus::Inactive)->values(),
        ]);
    }

    public function create(): View
    {
        return view('coach.clients.form', ['client' => new User]);
    }

    public function store(ClientRequest $request): RedirectResponse
    {
        $coach = $request->user();

        $client = DB::transaction(function () use ($request, $coach) {
            $client = User::create($request->clientData() + [
                'role' => Role::Client,
                'company_id' => $coach->company_id,
                'coach_id' => $coach->id,
            ]);

            if ($request->filled('notes')) {
                $coach->authoredNotes()->create([
                    'client_id' => $client->id,
                    'noted_on' => today(),
                    'body' => $request->input('notes'),
                ]);
            }

            return $client;
        });

        return redirect()->route('coach.clients.show', $client)->with('status', __('Client created.'));
    }

    public function show(Request $request, User $client): View
    {
        Gate::authorize('manage-client', $client);

        $range = DateRange::fromRequest($request);
        $activity = new ClientActivity($client, $range);
        $this->markViewed($client);

        return view('coach.clients.show', [
            'client' => $client,
            'range' => $range,
            'tab' => null,
            'charts' => collect(SuiteApp::cases())->mapWithKeys(fn (SuiteApp $app) => [$app->value => $activity->chart($app)]),
            'notes' => $this->notes($request->user(), $client),
        ]);
    }

    public function app(Request $request, User $client, string $app): View
    {
        Gate::authorize('manage-client', $client);

        $suiteApp = SuiteApp::fromSlug($app) ?? abort(404);
        $range = DateRange::fromRequest($request);
        $activity = new ClientActivity($client, $range);
        $this->markViewed($client);

        return view('coach.clients.app', [
            'client' => $client,
            'range' => $range,
            'tab' => $suiteApp,
            'chart' => $activity->chart($suiteApp, full: true),
            'summary' => $activity->summary($suiteApp),
            'details' => $activity->details($suiteApp),
            'sharedScripts' => $suiteApp === SuiteApp::ScriptBuilder ? $activity->sharedScripts() : collect(),
            'notes' => $this->notes($request->user(), $client),
        ]);
    }

    public function edit(User $client): View
    {
        Gate::authorize('manage-client', $client);

        return view('coach.clients.form', ['client' => $client]);
    }

    public function update(ClientRequest $request, User $client): RedirectResponse
    {
        Gate::authorize('manage-client', $client);

        $client->update($request->clientData());

        return redirect()->route('coach.clients.show', $client)->with('status', __('Client saved.'));
    }

    public function destroy(User $client): RedirectResponse
    {
        Gate::authorize('manage-client', $client);

        $client->delete();

        return redirect()->route('coach.clients.index')->with('status', __('Client removed.'));
    }

    private function notes(User $coach, User $client)
    {
        return $coach->authoredNotes()->where('client_id', $client->id)->latest('noted_on')->latest('id')->get();
    }

    private function markViewed(User $client): void
    {
        $client->forceFill(['coach_viewed_at' => now()])->saveQuietly();
    }

    private function nextBirthday(?Carbon $birthday): ?Carbon
    {
        if (! $birthday) {
            return null;
        }

        $next = $birthday->copy()->year(today()->year);

        return $next->lt(today()) ? $next->addYear() : $next;
    }

    private function birthdayThisWeek(?Carbon $birthday): bool
    {
        $next = $this->nextBirthday($birthday);

        return $next !== null && $next->lte(today()->addDays(6));
    }
}
