<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\IncidentOccurrence;
use App\Models\KpiScorecard;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Officer dashboard. Every figure goes through the location-scoped models, so a
 * user only sees their own location (Super-Admin sees all), and incidents follow
 * the same "own incidents unless View All Incidents" rule as the incidents list.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $now  = now();

        $incidents = fn (): Builder => IncidentOccurrence::query()
            ->unless($user->can('View All Incidents'), fn ($q) => $q->where('user_id', $user->id));

        // Incidents this week (Monday to now) against the same span last week.
        $weekStart     = $now->copy()->startOfWeek(Carbon::MONDAY);
        $incidentsWeek = $incidents()->whereBetween('occurred_at', [$weekStart, $now])->count();
        $incidentsPrev = $incidents()->whereBetween('occurred_at', [$weekStart->copy()->subWeek(), $now->copy()->subWeek()])->count();

        $today     = $this->deployment($now);
        $yesterday = $this->deployment($now->copy()->subDay());

        $recent = $incidents()
            ->with(['incidentType', 'location', 'severity', 'status'])
            ->latest('occurred_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'incidentsWeek' => $incidentsWeek,
            'incidentsDiff' => $incidentsWeek - $incidentsPrev,
            'today'         => $today,
            'yesterdayPct'  => $yesterday['pct'],
            'recent'        => $recent,
            'week'          => $this->week($incidents()->with('severity'), $weekStart, $now),
            'missingCards'  => $user->can('Submit KPI Scorecard') ? $this->missingScorecards($now) : null,
        ]);
    }

    /** Contracted vs deployed guards from the day's scorecard deployment lines. */
    private function deployment(Carbon $day): array
    {
        $cards = KpiScorecard::with(['lines' => fn ($q) => $q->whereNotNull('place_id')])
            ->whereDate('date', $day->toDateString())
            ->get();

        $contracted = (int) $cards->flatMap->lines->sum('target');
        $deployed   = (int) $cards->flatMap->lines->sum('scored');

        return [
            'submitted'  => $cards->isNotEmpty(),
            'contracted' => $contracted,
            'deployed'   => $deployed,
            'shortfall'  => max($contracted - $deployed, 0),
            'pct'        => $contracted > 0 ? round($deployed / $contracted * 100, 1) : null,
        ];
    }

    /** One bar per day Monday–Sunday of the current week. */
    private function week(Builder $query, Carbon $weekStart, Carbon $now): array
    {
        $critical = array_map('strtolower', (array) config('security_analytics.critical_severities', ['high', 'urgent']));

        $byDay = $query->whereBetween('occurred_at', [$weekStart, $weekStart->copy()->endOfWeek(Carbon::SUNDAY)])
            ->get()
            ->groupBy(fn ($i) => $i->occurred_at->toDateString());

        $days = collect(CarbonPeriod::create($weekStart, $weekStart->copy()->addDays(6)))->map(function (Carbon $day) use ($byDay, $critical, $now) {
            $items = $byDay[$day->toDateString()] ?? collect();

            return [
                'label'    => $day->format('D'),
                'count'    => $items->count(),
                'critical' => $items->contains(fn ($i) => in_array(strtolower((string) $i->severity?->name), $critical, true)),
                'future'   => $day->gt($now->copy()->endOfDay()),
            ];
        });

        $max = max($days->max('count'), 1);

        return $days->map(fn ($d) => $d + ['height' => $d['count'] ? max(round($d['count'] / $max * 100), 8) : 0])->all();
    }

    /** Buildings in the user's location with no scorecard for today. */
    private function missingScorecards(Carbon $now): array
    {
        $buildings = Building::count();
        $submitted = KpiScorecard::whereDate('date', $now->toDateString())
            ->whereNotNull('building_id')
            ->distinct()
            ->count('building_id');

        return ['buildings' => $buildings, 'missing' => max($buildings - $submitted, 0)];
    }
}
