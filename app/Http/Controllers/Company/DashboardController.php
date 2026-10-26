<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AppActivity;
use App\Models\User;
use App\Support\Options;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public const SORTS = ['clients', 'country', 'language', 'age'];

    public function index(Request $request): View
    {
        $company = $request->user()->company;
        $sort = in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'clients';

        $clients = $company->clients()->whereNotNull('coach_id')->get(['id', 'coach_id', 'country', 'language', 'age']);
        $clientIds = $clients->pluck('id');

        $usingSuite = AppActivity::whereIn('client_id', $clientIds)
            ->where('activity_date', '>=', now()->subDays(13)->toDateString())
            ->distinct()
            ->pluck('client_id')
            ->flip();

        $coaches = $company->coaches()->orderBy('name')->get()->map(function (User $coach) use ($clients, $usingSuite) {
            $own = $clients->where('coach_id', $coach->id);

            return (object) [
                'coach' => $coach,
                'clients' => $own->count(),
                'active' => $own->filter(fn ($client) => $usingSuite->has($client->id))->count(),
            ];
        });

        $coaches = ($sort === 'country'
            ? $coaches->sortBy(fn ($row) => [$row->coach->country === null, $row->coach->country])
            : $coaches->sortByDesc('clients'))->values();

        $group = $sort === 'clients' ? 'country' : $sort;
        $languages = Options::languages();

        $demographics = $clients
            ->groupBy(fn ($client) => match ($group) {
                'country' => $client->country ?: __('Not given'),
                'language' => $languages[$client->language] ?? __('Not given'),
                'age' => Options::ageBand($client->age),
            })
            ->map->count()
            ->sortDesc();

        return view('company.dashboard', [
            'company' => $company,
            'sort' => $sort,
            'group' => $group,
            'totals' => [
                'coaches' => $coaches->count(),
                'clients' => $clients->count(),
                'using' => $usingSuite->count(),
            ],
            'coaches' => $coaches,
            'demographics' => $demographics,
            'chart' => $this->monthlyActivity($clientIds),
        ]);
    }

    private function monthlyActivity($clientIds): array
    {
        $months = collect(range(11, 0))->map(fn ($back) => now()->startOfMonth()->subMonths($back));

        $perMonth = AppActivity::whereIn('client_id', $clientIds)
            ->where('activity_date', '>=', $months->first()->toDateString())
            ->selectRaw('activity_date, count(*) as total')
            ->groupBy('activity_date')
            ->get()
            ->groupBy(fn ($row) => $row->activity_date->format('Y-m'))
            ->map->sum('total');

        return [
            'type' => 'bar',
            'labels' => $months->map->translatedFormat('M Y')->all(),
            'datasets' => [[
                'label' => __('App sessions'),
                'data' => $months->map(fn ($month) => (int) ($perMonth[$month->format('Y-m')] ?? 0))->all(),
            ]],
        ];
    }
}
