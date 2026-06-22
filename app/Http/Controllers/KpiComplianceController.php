<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\KpiScorecard;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KpiComplianceController extends Controller
{
    /**
     * Money-free deployment compliance — contracted guards vs deployed guards.
     * Built from the scorecard Deployment lines (place_id set: target = estimated
     * guards, scored = deployed). No billing/invoice/vendor money anywhere.
     */
    public function index(Request $request)
    {
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->month)
            ? $request->month
            : Carbon::now()->format('Y-m');

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $cards = KpiScorecard::with(['lines' => fn ($q) => $q->whereNotNull('place_id')])
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->when($request->location_id, fn ($q, $v) => $q->where('location_id', $v))
            ->when($request->building_id, fn ($q, $v) => $q->where('building_id', $v))
            ->orderBy('date')
            ->get();

        // Day-by-day rows (one per scorecard).
        $days = $cards->map(function (KpiScorecard $c) {
            $contracted = (float) $c->lines->sum('target');
            $deployed   = (float) $c->lines->sum('scored');

            return [
                'date'       => $c->date,
                'location'   => $c->location_name,
                'building'   => $c->building_name,
                'contracted' => $contracted,
                'deployed'   => $deployed,
                'pct'        => $contracted > 0 ? round($deployed / $contracted * 100, 1) : 0,
                'shortfall'  => max($contracted - $deployed, 0),
            ];
        });

        // Per-beat aggregate across the month (keyed by snapshot place name).
        $beats = $cards->flatMap->lines
            ->groupBy('criteria')
            ->map(function ($lines, $name) {
                $contracted = (float) $lines->sum('target');
                $deployed   = (float) $lines->sum('scored');

                return [
                    'name'       => $name,
                    'contracted' => $contracted,
                    'deployed'   => $deployed,
                    'pct'        => $contracted > 0 ? round($deployed / $contracted * 100, 1) : 0,
                ];
            })
            ->sortByDesc('contracted')
            ->values();

        $totalContracted = (float) $days->sum('contracted');
        $totalDeployed   = (float) $days->sum('deployed');

        return view('kpi.compliance.index', [
            'month'       => $month,
            'monthLabel'  => $start->format('F Y'),
            'filters'     => $request->only(['location_id', 'building_id']),
            'locations'   => Location::orderBy('name')->get(),
            'buildings'   => Building::orderBy('name')->get(['id', 'name']),
            'days'        => $days,
            'beats'       => $beats,
            'contracted'  => $totalContracted,
            'deployed'    => $totalDeployed,
            'pct'         => $totalContracted > 0 ? round($totalDeployed / $totalContracted * 100, 1) : 0,
            'shortfall'   => max($totalContracted - $totalDeployed, 0),
        ]);
    }
}
