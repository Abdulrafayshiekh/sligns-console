<?php

namespace App\Support;

use App\Enums\SuiteApp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ClientActivity
{
    private array $rows = [];

    public function __construct(private User $client, private DateRange $range) {}

    public function rows(SuiteApp $app): Collection
    {
        return $this->rows[$app->value] ??= $this->client->activities()
            ->where('app', $app)
            ->where('activity_date', '>=', $this->range->from->toDateString())
            ->where('activity_date', '<', $this->range->to->copy()->addDay()->toDateString())
            ->orderBy('activity_date')
            ->get();
    }

    public function chart(SuiteApp $app, bool $full = false): array
    {
        $byDay = $this->rows($app)->groupBy(fn ($row) => $row->activity_date->toDateString());
        $labels = [];
        $primary = [];
        $secondary = [];

        foreach ($this->range->days() as $day) {
            $rows = $byDay->get($day->toDateString(), collect());
            $labels[] = $day->translatedFormat('M j');

            $primary[] = match ($app) {
                SuiteApp::Scintilla => $rows->sum('quantity'),
                SuiteApp::TargetPro, SuiteApp::Momentum => $rows->isEmpty() ? null : round($rows->avg('value'), 1),
                SuiteApp::Lotus => round($rows->sum('value')),
                SuiteApp::ScriptBuilder => $rows->sum('quantity'),
            };

            $secondary[] = $rows->sum('quantity');
        }

        $datasets = [[
            'label' => match ($app) {
                SuiteApp::Scintilla => __('Messages opened'),
                SuiteApp::TargetPro => __('Average goal progress (%)'),
                SuiteApp::Momentum => __('Emotional status (1–10)'),
                SuiteApp::Lotus => __('Minutes'),
                SuiteApp::ScriptBuilder => __('Scripts'),
            },
            'data' => $primary,
        ]];

        if ($full && $app === SuiteApp::Lotus) {
            $datasets[] = ['label' => __('Sessions'), 'data' => $secondary, 'type' => 'bar', 'yAxisID' => 'y1'];
        }

        return [
            'type' => 'line',
            'empty' => $this->rows($app)->isEmpty(),
            'labels' => $labels,
            'datasets' => $datasets,
            'max' => match ($app) {
                SuiteApp::TargetPro => 100,
                SuiteApp::Momentum => 10,
                default => null,
            },
        ];
    }

    public function summary(SuiteApp $app): array
    {
        $rows = $this->rows($app);
        $last = $this->client->activities()->where('app', $app)->max('activity_date');
        $lastLabel = $last ? Carbon::parse($last)->translatedFormat('j M Y') : __('Never');

        return match ($app) {
            SuiteApp::Scintilla => [
                __('Messages opened in range') => $rows->sum('quantity'),
                __('Subjects chosen') => $rows->pluck('label')->filter()->unique()->count(),
                __('Last used') => $lastLabel,
            ],
            SuiteApp::TargetPro => [
                __('Targets and goals') => $rows->pluck('label')->filter()->unique()->count(),
                __('Average progress') => $rows->isEmpty() ? '—' : round($rows->avg('value')).'%',
                __('Last update') => $lastLabel,
            ],
            SuiteApp::Momentum => [
                __('Check-ins in range') => $rows->count(),
                __('Average status') => $rows->isEmpty() ? '—' : round($rows->avg('value'), 1).' / 10',
                __('Last check-in') => $lastLabel,
            ],
            SuiteApp::Lotus => [
                __('Sessions in range') => $rows->sum('quantity'),
                __('Minutes in range') => round($rows->sum('value')),
                __('Last session') => $lastLabel,
            ],
            SuiteApp::ScriptBuilder => [
                __('Scripts in range') => $rows->sum('quantity'),
                __('Areas') => $rows->pluck('label')->filter()->unique()->count(),
                __('Last script') => $lastLabel,
            ],
        };
    }

    public function details(SuiteApp $app): Collection
    {
        $rows = $this->rows($app);

        return match ($app) {
            SuiteApp::Scintilla => $rows->groupBy('label')->map(fn ($group, $subject) => (object) [
                'name' => $subject,
                'times' => $group->count(),
                'opened' => $group->sum('quantity'),
            ])->sortByDesc('times')->values(),
            SuiteApp::TargetPro => $rows->groupBy('label')->map(fn ($group, $goal) => (object) [
                'name' => $goal,
                'progress' => (int) round($group->last()->value ?? 0),
                'updated' => $group->last()->activity_date,
            ])->sortByDesc('progress')->values(),
            SuiteApp::Momentum => $rows->sortByDesc('activity_date')->take(10)->values(),
            SuiteApp::Lotus => $rows->sortByDesc('activity_date')->take(10)->values(),
            SuiteApp::ScriptBuilder => $rows->groupBy('label')->map(fn ($group, $area) => (object) [
                'name' => $area,
                'scripts' => $group->sum('quantity'),
            ])->sortByDesc('scripts')->values(),
        };
    }

    public function sharedScripts(): Collection
    {
        return $this->rows(SuiteApp::ScriptBuilder)
            ->where('shared', true)
            ->whereNotNull('content')
            ->sortByDesc('activity_date')
            ->values();
    }
}
