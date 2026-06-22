<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\KpiScorecard;
use App\Models\Location;
use App\Support\KpiScoring;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KpiReportController extends Controller
{
    /** Analytical reports — weekly / monthly / quarterly / YTD. */
    public function index(Request $request)
    {
        $range = in_array($request->range, ['week', 'month', 'quarter', 'ytd'], true)
            ? $request->range
            : 'month';

        [$start, $end, $rangeLabel] = $this->window($range);

        $cards      = $this->cards($start, $end, $request->location_id, $request->building_id);
        $agg        = $this->aggregate($cards);
        $trend      = $this->trend($range, $request->location_id, $request->building_id);
        $deployment = $this->deploymentByLocation($cards);

        return view('kpi.reports.index', [
            'range'       => $range,
            'rangeLabel'  => $rangeLabel,
            'filters'     => $request->only(['location_id', 'building_id']),
            'locations'   => Location::orderBy('name')->get(),
            'buildings'   => Building::orderBy('name')->get(['id', 'name']),
            'agg'         => $agg,
            'trend'       => $trend,
            'deployment'  => $deployment,
        ]);
    }

    /** Custom printable monthly report — month + location + building. */
    public function monthly(Request $request)
    {
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->month)
            ? $request->month
            : Carbon::now()->format('Y-m');

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $cards = $this->cards($start, $end, $request->location_id, $request->building_id);

        $reports = $cards->map(fn (KpiScorecard $c) => [
            'card'      => $c,
            'breakdown' => KpiScoring::breakdown($c),
        ]);

        return view('kpi.reports.monthly', [
            'month'      => $month,
            'monthLabel' => $start->format('F Y'),
            'filters'    => $request->only(['location_id', 'building_id']),
            'locations'  => Location::orderBy('name')->get(),
            'buildings'  => Building::orderBy('name')->get(['id', 'name']),
            'reports'    => $reports,
            'rollup'     => $this->aggregate($cards),
        ]);
    }

    /** Scorecards in a date window, with the active location/building filters. */
    private function cards($start, $end, $locationId, $buildingId): Collection
    {
        return KpiScorecard::with('lines.group')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->when($locationId, fn ($q, $v) => $q->where('location_id', $v))
            ->when($buildingId, fn ($q, $v) => $q->where('building_id', $v))
            ->orderBy('date')
            ->get();
    }

    /** Resolve a range keyword into [start, end, label]. */
    private function window(string $range): array
    {
        $now = Carbon::now();

        return match ($range) {
            'week'    => [(clone $now)->startOfWeek(), (clone $now)->endOfWeek(), 'This week'],
            'quarter' => [(clone $now)->startOfQuarter(), (clone $now)->endOfQuarter(), 'Q' . $now->quarter . ' ' . $now->year],
            'ytd'     => [(clone $now)->startOfYear(), (clone $now)->endOfYear(), $now->year . ' YTD'],
            default   => [(clone $now)->startOfMonth(), (clone $now)->endOfMonth(), $now->format('F Y')],
        };
    }

    /**
     * Proposed vs actual deployment guard-days, summed per location.
     * Uses only the deployment lines (place_id set: target = proposed guards,
     * scored = actual deployed).
     *
     * @return array{labels:array<string>, proposed:array<float>, actual:array<float>}
     */
    private function deploymentByLocation(Collection $cards): array
    {
        $byLocation = []; // location_name => ['proposed' => x, 'actual' => y]

        foreach ($cards as $card) {
            $deploymentLines = $card->lines->whereNotNull('place_id');

            if ($deploymentLines->isEmpty()) {
                continue;
            }

            $name = $card->location_name;
            $byLocation[$name] ??= ['proposed' => 0, 'actual' => 0];
            $byLocation[$name]['proposed'] += (float) $deploymentLines->sum('target');
            $byLocation[$name]['actual']   += (float) $deploymentLines->sum('scored');
        }

        ksort($byLocation);

        return [
            'labels'   => array_keys($byLocation),
            'proposed' => array_column($byLocation, 'proposed'),
            'actual'   => array_column($byLocation, 'actual'),
        ];
    }

    /**
     * Aggregate a set of scorecards into overall % + per-group averages.
     *
     * @return array{count:int, overall_pct:float, groups:array, best:?array, worst:?array}
     */
    private function aggregate(Collection $cards): array
    {
        if ($cards->isEmpty()) {
            return ['count' => 0, 'overall_pct' => 0, 'groups' => [], 'best' => null, 'worst' => null];
        }

        $overallSum = 0;
        $groupAcc   = []; // name => [weight, attainmentSum, count]

        foreach ($cards as $card) {
            $b = KpiScoring::breakdown($card);
            $overallSum += $b['overall_pct'];

            foreach ($b['groups'] as $g) {
                $acc = $groupAcc[$g['name']] ?? ['weight' => $g['weight'], 'sum' => 0, 'n' => 0];
                $acc['sum'] += $g['attainment'];
                $acc['n']   += 1;
                $groupAcc[$g['name']] = $acc;
            }
        }

        $groups = collect($groupAcc)->map(fn ($a, $name) => [
            'name'       => $name,
            'weight'     => $a['weight'],
            'attainment' => round($a['sum'] / max($a['n'], 1), 1),
        ])->sortByDesc('weight')->values()->all();

        $sortedByScore = collect($groups)->sortByDesc('attainment')->values();

        return [
            'count'       => $cards->count(),
            'overall_pct' => round($overallSum / $cards->count(), 1),
            'groups'      => $groups,
            'best'        => $sortedByScore->first(),
            'worst'       => $sortedByScore->last(),
        ];
    }

    /**
     * Overall % across the last 6 periods of the chosen unit, for the trend chart.
     *
     * @return array{labels:array<string>, values:array<float>}
     */
    private function trend(string $range, $locationId, $buildingId): array
    {
        $now  = Carbon::now();
        $unit = $range === 'ytd' ? 'month' : $range; // YTD trends month-by-month

        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            [$start, $end, $label] = match ($unit) {
                'week'    => [(clone $now)->subWeeks($i)->startOfWeek(), (clone $now)->subWeeks($i)->endOfWeek(), (clone $now)->subWeeks($i)->format('d M')],
                'quarter' => [(clone $now)->subQuarters($i)->startOfQuarter(), (clone $now)->subQuarters($i)->endOfQuarter(), 'Q' . (clone $now)->subQuarters($i)->quarter . " '" . (clone $now)->subQuarters($i)->format('y')],
                default   => [(clone $now)->subMonths($i)->startOfMonth(), (clone $now)->subMonths($i)->endOfMonth(), (clone $now)->subMonths($i)->format('M')],
            };

            $cards = $this->cards($start, $end, $locationId, $buildingId);
            $labels[] = $label;
            $values[] = $this->aggregate($cards)['overall_pct'];
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
