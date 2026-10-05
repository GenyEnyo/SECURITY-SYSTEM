<?php

namespace App\Services;

use App\Models\KpiScorecard;
use App\Support\KpiScoring;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Read-only analytics over incidents, KPI scorecards and deployments,
 * exposed to the management (mgt) system through /api/v1/analytics/*.
 *
 * Every figure respects the period (from/to, inclusive) and optional location filter
 * and ignores soft-deleted rows.
 */
class SecurityAnalyticsService
{
    private Carbon $from;
    private Carbon $to;
    private ?int $locationId = null;

    /** Per-period memo so the combined dashboard doesn't repeat heavy queries. */
    private array $memo = [];

    public function forPeriod(Carbon $from, Carbon $to, ?int $locationId = null): self
    {
        $this->from       = $from->copy()->startOfDay();
        $this->to         = $to->copy()->endOfDay();
        $this->locationId = $locationId;
        $this->memo       = [];

        return $this;
    }

    public function dashboard(): array
    {
        return [
            'summary'     => $this->summary(),
            'incidents'   => $this->incidents(),
            'deployment'  => $this->deployment(),
            'kpi'         => $this->kpi(),
            'contractors' => $this->contractors(),
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Headline KPI cards                                                 */
    /* ------------------------------------------------------------------ */

    public function summary(): array
    {
        [$prevFrom, $prevTo] = $this->previousWindow();

        $current  = $this->incidentCount($this->from, $this->to);
        $previous = $this->incidentCount($prevFrom, $prevTo);

        $open = $this->incidentBase()->whereNull('i.acknowledged_at');

        $ackMinutes = $this->incidentBase()
            ->whereBetween('i.occurred_at', [$this->from, $this->to])
            ->whereNotNull('i.acknowledged_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, i.created_at, i.acknowledged_at)) AS m')
            ->value('m');

        $guards     = $this->deploymentTotals($this->from, $this->to);
        $prevGuards = $this->deploymentTotals($prevFrom, $prevTo);

        $kpi     = $this->scorecardBreakdowns();
        $prevKpi = $this->scorecardBreakdowns($prevFrom, $prevTo);

        $onDuty = DB::table('deployments as d')
            ->join('buildings as b', 'b.id', '=', 'd.building_id')
            ->join('shifts as sh', 'sh.id', '=', 'd.shift_id')
            ->where('d.start_at', '<=', now())
            ->where('d.end_at', '>=', now())
            ->when($this->locationId, fn ($q, $v) => $q->where('b.location_id', $v))
            ->groupBy('sh.name')
            ->selectRaw('sh.name AS shift, SUM(d.number_of_guards) AS guards')
            ->pluck('guards', 'shift')
            ->map(fn ($v) => (int) $v);

        return [
            'incidents' => [
                'count'      => $current,
                'previous'   => $previous,
                'change_pct' => $this->change($current, $previous),
            ],
            'open_incidents' => [
                'count'          => (clone $open)->count(),
                'older_than_24h' => (clone $open)->where('i.created_at', '<', now()->subDay())->count(),
            ],
            'avg_ack_hours' => $ackMinutes !== null ? round($ackMinutes / 60, 1) : null,
            'deployment_compliance' => $guards + ['previous_pct' => $prevGuards['pct']],
            'kpi_score' => [
                'avg_pct'          => $this->avgOverall($kpi),
                'scorecards'       => $kpi->count(),
                'previous_avg_pct' => $this->avgOverall($prevKpi),
            ],
            'guards_on_duty' => [
                'total'    => (int) $onDuty->sum(),
                'by_shift' => $onDuty,
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Incident analytics                                                 */
    /* ------------------------------------------------------------------ */

    public function incidents(): array
    {
        $critical = $this->criticalSeverities();

        $byType = $this->incidentsInRange()
            ->join('incident_types as t', 't.id', '=', 'i.incident_type_id')
            ->groupBy('t.name')
            ->selectRaw('t.name AS label, COUNT(*) AS count')
            ->orderByDesc('count')
            ->get();

        $bySeverity = $this->incidentsInRange()
            ->join('severities as s', 's.id', '=', 'i.severity_id')
            ->groupBy('s.id', 's.name', 's.color')
            ->selectRaw('s.name AS label, s.color AS color, COUNT(*) AS count')
            ->orderBy('s.id')
            ->get();

        $statusCounts = $this->incidentsInRange()
            ->groupBy('i.incident_status_id')
            ->selectRaw('i.incident_status_id AS id, COUNT(*) AS count')
            ->pluck('count', 'id');

        $byStatus = DB::table('incident_statuses')->orderBy('id')->get(['id', 'name'])
            ->map(fn ($s) => ['label' => $s->name, 'count' => (int) ($statusCounts[$s->id] ?? 0)]);

        $byLocation = $this->incidentsInRange()
            ->join('locations as l', 'l.id', '=', 'i.location_id')
            ->groupBy('l.name')
            ->selectRaw('l.name AS label, COUNT(*) AS count')
            ->orderByDesc('count')
            ->get();

        $byBuilding = $this->incidentsInRange()
            ->join('locations as l', 'l.id', '=', 'i.location_id')
            ->leftJoin('buildings as b', 'b.id', '=', 'i.building_id')
            ->groupBy('l.name', 'b.name')
            ->selectRaw("COALESCE(b.name, 'Unspecified') AS building, l.name AS location, COUNT(*) AS count")
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $hours = $this->incidentsInRange()
            ->groupByRaw('HOUR(i.occurred_at)')
            ->selectRaw('HOUR(i.occurred_at) AS h, COUNT(*) AS count')
            ->pluck('count', 'h');

        // MySQL DAYOFWEEK: 1 = Sunday … 7 = Saturday. Present Monday-first.
        $days = $this->incidentsInRange()
            ->groupByRaw('DAYOFWEEK(i.occurred_at)')
            ->selectRaw('DAYOFWEEK(i.occurred_at) AS d, COUNT(*) AS count')
            ->pluck('count', 'd');

        $weekday = collect([2 => 'Mon', 3 => 'Tue', 4 => 'Wed', 5 => 'Thu', 6 => 'Fri', 7 => 'Sat', 1 => 'Sun'])
            ->map(fn ($label, $d) => ['label' => $label, 'count' => (int) ($days[$d] ?? 0)])
            ->values();

        $recentCritical = $this->incidentsInRange()
            ->join('incident_types as t', 't.id', '=', 'i.incident_type_id')
            ->join('severities as s', 's.id', '=', 'i.severity_id')
            ->join('incident_statuses as st', 'st.id', '=', 'i.incident_status_id')
            ->join('locations as l', 'l.id', '=', 'i.location_id')
            ->leftJoin('buildings as b', 'b.id', '=', 'i.building_id')
            ->leftJoin('places as p', 'p.id', '=', 'i.place_id')
            ->whereIn(DB::raw('LOWER(s.name)'), $critical)
            ->orderByDesc('i.occurred_at')
            ->limit(10)
            ->get([
                'i.id', 'i.occurred_at', 'i.acknowledged_at',
                't.name as type', 's.name as severity', 's.color as severity_color',
                'st.name as status', 'l.name as location', 'b.name as building', 'p.name as place',
            ]);

        return [
            'total'           => $this->incidentCount($this->from, $this->to),
            'by_type'         => $this->intCounts($byType),
            'by_severity'     => $this->intCounts($bySeverity),
            'by_status'       => $byStatus,
            'trend'           => $this->incidentTrend(),
            'by_location'     => $this->intCounts($byLocation),
            'by_building'     => $this->intCounts($byBuilding),
            'by_hour'         => collect(range(0, 23))->map(fn ($h) => (int) ($hours[$h] ?? 0)),
            'by_weekday'      => $weekday,
            'recent_critical' => $recentCritical,
            'ack_sla'         => $this->ackSla(),
            'risk_heatmap'    => $this->riskHeatmap(),
            'repeat_hotspots' => $this->repeatHotspots(),
            'rate_by_location' => $this->incidentRateByLocation(),
            'day_vs_night'    => $this->dayVsNight(),
            'comparisons'     => $this->comparisons(),
        ];
    }

    /** Incidents per period, with a separate High + Urgent series. */
    private function incidentTrend(): array
    {
        [$granularity, $format, $periods] = $this->periods();

        $rows = $this->incidentsInRange()
            ->join('severities as s', 's.id', '=', 'i.severity_id')
            ->selectRaw("DATE_FORMAT(i.occurred_at, '{$format}') AS period, COUNT(*) AS total")
            ->selectRaw($this->criticalCaseSql('s.name').' AS critical', $this->criticalSeverities())
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        return [
            'granularity' => $granularity,
            'labels'      => array_values($periods),
            'total'       => array_map(fn ($k) => (int) ($rows[$k]->total ?? 0), array_keys($periods)),
            'critical'    => array_map(fn ($k) => (int) ($rows[$k]->critical ?? 0), array_keys($periods)),
        ];
    }

    /** % of incidents acknowledged within the configured SLA per severity. */
    private function ackSla(): array
    {
        $targets = array_change_key_case((array) config('security_analytics.ack_sla_hours'));
        $now     = now()->toDateTimeString();

        $rows = $this->incidentsInRange()
            ->join('severities as s', 's.id', '=', 'i.severity_id')
            ->selectRaw('s.id AS sid, s.name AS severity, i.acknowledged_at IS NOT NULL AS acked')
            ->selectRaw('TIMESTAMPDIFF(MINUTE, i.created_at, COALESCE(i.acknowledged_at, ?)) AS mins', [$now])
            ->orderBy('s.id')
            ->get();

        $bySeverity = $rows->groupBy('severity')->map(function (Collection $items, $severity) use ($targets) {
            $target = $targets[strtolower($severity)] ?? null;
            $met = $breached = $pending = 0;

            foreach ($items as $row) {
                $within = $target === null || $row->mins <= $target * 60;
                if ($row->acked) {
                    $within ? $met++ : $breached++;
                } else {
                    $within ? $pending++ : $breached++;
                }
            }

            return [
                'severity'     => $severity,
                'target_hours' => $target,
                'total'        => $items->count(),
                'met'          => $met,
                'breached'     => $breached,
                'pending'      => $pending,
                'pct'          => $this->pct($met, $met + $breached),
            ];
        })->values();

        $met      = $bySeverity->sum('met');
        $breached = $bySeverity->sum('breached');

        return [
            'overall_pct' => $this->pct($met, $met + $breached),
            'breached'    => $breached,
            'by_severity' => $bySeverity,
        ];
    }

    /** Severity-weighted risk score per location per month. */
    private function riskHeatmap(): array
    {
        $weights = array_change_key_case((array) config('security_analytics.severity_weights'));
        $months  = $this->monthKeys($this->from, $this->to);

        $rows = $this->incidentsInRange()
            ->join('locations as l', 'l.id', '=', 'i.location_id')
            ->join('severities as s', 's.id', '=', 'i.severity_id')
            ->groupBy('l.name', 'month', 's.name')
            ->selectRaw("l.name AS location, DATE_FORMAT(i.occurred_at, '%Y-%m') AS month, s.name AS severity, COUNT(*) AS count")
            ->get();

        $scores = [];
        foreach ($rows as $row) {
            $w = $weights[strtolower($row->severity)] ?? 1;
            $scores[$row->location][$row->month] = ($scores[$row->location][$row->month] ?? 0) + $w * $row->count;
        }

        $locations = DB::table('locations')
            ->when($this->locationId, fn ($q, $v) => $q->where('id', $v))
            ->orderBy('name')
            ->pluck('name');

        return [
            'months' => array_values($months),
            'rows'   => $locations->map(fn ($name) => [
                'location' => $name,
                'scores'   => array_map(fn ($m) => (int) ($scores[$name][$m] ?? 0), array_keys($months)),
                'total'    => (int) array_sum($scores[$name] ?? []),
            ])->sortByDesc('total')->values(),
        ];
    }

    /** Beats/buildings with repeated incidents in the rolling window ending at the period end. */
    private function repeatHotspots(): array
    {
        $threshold = (int) config('security_analytics.repeat_threshold', 3);
        $days      = (int) config('security_analytics.repeat_window_days', 30);
        $end       = $this->to->copy()->min(now());
        $start     = $end->copy()->subDays($days)->startOfDay();

        $rows = $this->incidentBase()
            ->whereBetween('i.occurred_at', [$start, $end])
            ->join('locations as l', 'l.id', '=', 'i.location_id')
            ->leftJoin('buildings as b', 'b.id', '=', 'i.building_id')
            ->leftJoin('places as p', 'p.id', '=', 'i.place_id')
            ->groupBy('l.name', 'b.name', 'p.name')
            ->selectRaw("l.name AS location, COALESCE(b.name, 'Unspecified') AS building, p.name AS place, COUNT(*) AS count, MAX(i.occurred_at) AS last_at")
            ->havingRaw('COUNT(*) >= ?', [$threshold])
            ->orderByDesc('count')
            ->get();

        return [
            'window_days' => $days,
            'threshold'   => $threshold,
            'items'       => $this->intCounts($rows),
        ];
    }

    /** Incidents per 100 guard-shifts, per location. */
    private function incidentRateByLocation(): Collection
    {
        $incidents = $this->incidentsInRange()
            ->join('locations as l', 'l.id', '=', 'i.location_id')
            ->groupBy('l.name')
            ->selectRaw('l.name AS location, COUNT(*) AS count')
            ->pluck('count', 'location');

        $shifts = $this->guardShiftRows()->groupBy('location')->map->sum('guard_shifts');

        return $incidents->keys()->merge($shifts->keys())->unique()->sort()->values()
            ->map(fn ($loc) => [
                'location'     => $loc,
                'incidents'    => (int) ($incidents[$loc] ?? 0),
                'guard_shifts' => (int) ($shifts[$loc] ?? 0),
                'per_100'      => $this->rate((int) ($incidents[$loc] ?? 0), (int) ($shifts[$loc] ?? 0)),
            ]);
    }

    /** Incidents by time of day against guard strength per shift. */
    private function dayVsNight(): array
    {
        $start = (int) config('security_analytics.day_shift_start', 6);
        $end   = (int) config('security_analytics.day_shift_end', 18);

        $row = $this->incidentsInRange()
            ->selectRaw('SUM(CASE WHEN HOUR(i.occurred_at) >= ? AND HOUR(i.occurred_at) < ? THEN 1 ELSE 0 END) AS day_count', [$start, $end])
            ->selectRaw('COUNT(*) AS total')
            ->first();

        $day   = (int) ($row->day_count ?? 0);
        $night = (int) ($row->total ?? 0) - $day;

        $shifts = $this->guardShiftRows()->groupBy(fn ($r) => strtolower($r['shift']))->map->sum('guard_shifts');

        return [
            'day_hours' => sprintf('%02d:00-%02d:00', $start, $end),
            'day'   => ['incidents' => $day,   'guard_shifts' => (int) ($shifts['day'] ?? 0),   'per_100' => $this->rate($day, (int) ($shifts['day'] ?? 0))],
            'night' => ['incidents' => $night, 'guard_shifts' => (int) ($shifts['night'] ?? 0), 'per_100' => $this->rate($night, (int) ($shifts['night'] ?? 0))],
        ];
    }

    /** Month-over-month, year-over-year and rolling 30-day comparisons. */
    private function comparisons(): array
    {
        $to = $this->to->copy()->min(now()->endOfDay());

        $windows = [
            'mom' => [
                [$to->copy()->startOfMonth(), $to],
                [$to->copy()->subMonthNoOverflow()->startOfMonth(), $to->copy()->subMonthNoOverflow()],
            ],
            'yoy' => [
                [$this->from, $this->to],
                [$this->from->copy()->subYear(), $this->to->copy()->subYear()],
            ],
            'rolling_30d' => [
                [$to->copy()->subDays(29)->startOfDay(), $to],
                [$to->copy()->subDays(59)->startOfDay(), $to->copy()->subDays(30)->endOfDay()],
            ],
        ];

        return collect($windows)->map(function ($pair) {
            [[$cf, $ct], [$pf, $pt]] = $pair;
            $cur  = $this->incidentCount($cf, $ct);
            $prev = $this->incidentCount($pf, $pt);

            return [
                'current'                => ['from' => $cf->toDateString(), 'to' => $ct->toDateString(), 'incidents' => $cur, 'compliance_pct' => $this->deploymentTotals($cf, $ct)['pct']],
                'previous'               => ['from' => $pf->toDateString(), 'to' => $pt->toDateString(), 'incidents' => $prev, 'compliance_pct' => $this->deploymentTotals($pf, $pt)['pct']],
                'incidents_change_pct'   => $this->change($cur, $prev),
            ];
        })->all();
    }

    /* ------------------------------------------------------------------ */
    /*  Guard deployment compliance                                        */
    /* ------------------------------------------------------------------ */

    public function deployment(): array
    {
        [$granularity, $format, $periods] = $this->periods();

        $byLocation = $this->deploymentLines($this->from, $this->to)
            ->groupBy('s.location_name')
            ->selectRaw('s.location_name AS location, SUM(l.target) AS contracted, SUM(l.scored) AS deployed')
            ->orderBy('s.location_name')
            ->get()
            ->map(fn ($r) => $this->complianceRow((array) $r));

        $trendRows = $this->deploymentLines($this->from, $this->to)
            ->selectRaw("DATE_FORMAT(s.date, '{$format}') AS period, SUM(l.target) AS contracted, SUM(l.scored) AS deployed")
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $worstBeats = $this->deploymentLines($this->from, $this->to)
            ->groupBy('l.criteria', 's.building_name', 's.location_name')
            ->selectRaw('l.criteria AS beat, s.building_name AS building, s.location_name AS location')
            ->selectRaw('SUM(l.target) AS contracted, SUM(l.scored) AS deployed')
            ->selectRaw('SUM(GREATEST(CAST(l.target AS SIGNED) - CAST(l.scored AS SIGNED), 0)) AS shortfall')
            ->havingRaw('shortfall > 0')
            ->orderByDesc('shortfall')
            ->limit(10)
            ->get()
            ->map(fn ($r) => $this->complianceRow((array) $r) + ['shortfall' => (int) $r->shortfall]);

        $shiftRows = $this->guardShiftRows();
        $hoursBy   = fn (string $key) => $shiftRows->groupBy($key)
            ->map(fn ($g, $name) => [
                'label'        => $name,
                'guard_shifts' => (int) $g->sum('guard_shifts'),
                'guard_hours'  => (int) $g->sum('guard_hours'),
            ])->sortByDesc('guard_hours')->values();

        return [
            'totals'      => $this->deploymentTotals($this->from, $this->to),
            'by_location' => $byLocation,
            'trend'       => [
                'granularity' => $granularity,
                'labels'      => array_values($periods),
                'pct'         => array_map(function ($k) use ($trendRows) {
                    $r = $trendRows[$k] ?? null;
                    return $r ? $this->pct((int) $r->deployed, (int) $r->contracted) : null;
                }, array_keys($periods)),
            ],
            'worst_beats' => $worstBeats,
            'guard_hours' => [
                'shift_hours' => (int) config('security_analytics.shift_hours', 12),
                'total'       => (int) $shiftRows->sum('guard_hours'),
                'by_location' => $hoursBy('location'),
                'by_company'  => $hoursBy('company'),
                'by_shift'    => $hoursBy('shift'),
            ],
            'reporting_discipline' => $this->reportingDiscipline(),
        ];
    }

    /** % of expected building-days (up to today) that have a KPI scorecard submitted. */
    private function reportingDiscipline(): array
    {
        $end  = $this->to->copy()->min(now()->endOfDay());
        $days = $end->lt($this->from) ? 0 : (int) $this->from->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1;

        $buildings = DB::table('buildings as b')
            ->join('locations as l', 'l.id', '=', 'b.location_id')
            ->when($this->locationId, fn ($q, $v) => $q->where('b.location_id', $v))
            ->groupBy('l.name')
            ->selectRaw('l.name AS location, COUNT(*) AS buildings')
            ->pluck('buildings', 'location');

        $submitted = DB::table('kpi_scorecards as s')
            ->whereNull('s.deleted_at')
            ->whereNotNull('s.building_id')
            ->whereBetween('s.date', [$this->from->toDateString(), $end->toDateString()])
            ->when($this->locationId, fn ($q, $v) => $q->where('s.location_id', $v))
            ->groupBy('s.location_name')
            ->selectRaw('s.location_name AS location, COUNT(DISTINCT s.building_id, s.date) AS submitted')
            ->pluck('submitted', 'location');

        $rows = $buildings->map(fn ($count, $loc) => [
            'location'  => $loc,
            'expected'  => (int) $count * $days,
            'submitted' => (int) ($submitted[$loc] ?? 0),
            'pct'       => $this->pct((int) ($submitted[$loc] ?? 0), (int) $count * $days),
        ])->values();

        return [
            'days'        => $days,
            'expected'    => $rows->sum('expected'),
            'submitted'   => $rows->sum('submitted'),
            'pct'         => $this->pct($rows->sum('submitted'), $rows->sum('expected')),
            'by_location' => $rows,
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  KPI performance                                                    */
    /* ------------------------------------------------------------------ */

    public function kpi(): array
    {
        [$granularity, , $periods] = $this->periods();

        $cards = $this->scorecardBreakdowns();

        $groups = $cards->flatMap(fn ($c) => $c['breakdown']['groups'])
            ->groupBy('name')
            ->map(fn ($g, $name) => [
                'name'           => $name,
                'weight'         => (int) $g->first()['weight'],
                'avg_attainment' => round($g->avg('attainment'), 1),
                'avg_points'     => round($g->avg('points'), 1),
            ])
            ->sortByDesc('weight')
            ->values();

        $byLocation = $cards->groupBy('location')
            ->map(fn ($g, $loc) => [
                'location'   => $loc,
                'scorecards' => $g->count(),
                'avg_pct'    => $this->avgOverall($g),
            ])
            ->sortByDesc('avg_pct')
            ->values();

        $trend = $cards->groupBy(fn ($c) => $granularity === 'day' ? $c['date'] : substr($c['date'], 0, 7));

        return [
            'scorecards'  => $cards->count(),
            'avg_pct'     => $this->avgOverall($cards),
            'by_group'    => $groups,
            'best_group'  => $groups->sortByDesc('avg_attainment')->first(),
            'worst_group' => $groups->sortBy('avg_attainment')->first(),
            'by_location' => $byLocation,
            'trend'       => [
                'granularity' => $granularity,
                'labels'      => array_values($periods),
                'avg_pct'     => array_map(fn ($k) => isset($trend[$k]) ? $this->avgOverall($trend[$k]) : null, array_keys($periods)),
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Contractors                                                        */
    /* ------------------------------------------------------------------ */

    public function contractors(): array
    {
        $shiftRows = $this->guardShiftRows()->groupBy('company_id');

        $active = DB::table('deployments as d')
            ->join('buildings as b', 'b.id', '=', 'd.building_id')
            ->where('d.start_at', '<=', now())
            ->where('d.end_at', '>=', now())
            ->when($this->locationId, fn ($q, $v) => $q->where('b.location_id', $v))
            ->groupBy('d.security_company_id')
            ->selectRaw('d.security_company_id AS id, COUNT(*) AS deployments, SUM(d.number_of_guards) AS guards')
            ->get()
            ->keyBy('id');

        // Per-building figures, attributed to each contractor that guarded the building in the period.
        $complianceByBuilding = $this->deploymentLines($this->from, $this->to)
            ->whereNotNull('s.building_id')
            ->groupBy('s.building_id')
            ->selectRaw('s.building_id AS id, SUM(l.target) AS contracted, SUM(l.scored) AS deployed')
            ->get()
            ->keyBy('id');

        $incidentsByBuilding = $this->incidentsInRange()
            ->whereNotNull('i.building_id')
            ->groupBy('i.building_id')
            ->selectRaw('i.building_id AS id, COUNT(*) AS count')
            ->pluck('count', 'id');

        $cardsByBuilding = $this->scorecardBreakdowns()->groupBy('building_id');

        $companies = DB::table('security_companies')->orderBy('name')->get(['id', 'name', 'status'])
            ->map(function ($c) use ($shiftRows, $active, $complianceByBuilding, $incidentsByBuilding, $cardsByBuilding) {
                $rows      = $shiftRows[$c->id] ?? collect();
                $buildings = $rows->pluck('building_id')->unique();

                $contracted = $buildings->sum(fn ($b) => (int) ($complianceByBuilding[$b]->contracted ?? 0));
                $deployed   = $buildings->sum(fn ($b) => (int) ($complianceByBuilding[$b]->deployed ?? 0));
                $incidents  = $buildings->sum(fn ($b) => (int) ($incidentsByBuilding[$b] ?? 0));
                $cards      = $buildings->flatMap(fn ($b) => $cardsByBuilding[$b] ?? []);

                $kpiPct        = $this->avgOverall(collect($cards));
                $compliancePct = $this->pct($deployed, $contracted);
                $parts         = array_filter([$kpiPct, $compliancePct], fn ($v) => $v !== null);

                return [
                    'id'                 => $c->id,
                    'name'               => $c->name,
                    'status'             => $c->status,
                    'renewal_flag'       => $c->status === 'renewing',
                    'active_deployments' => (int) ($active[$c->id]->deployments ?? 0),
                    'guards_on_duty'     => (int) ($active[$c->id]->guards ?? 0),
                    'sites'              => $buildings->count(),
                    'guard_shifts'       => (int) $rows->sum('guard_shifts'),
                    'guard_hours'        => (int) $rows->sum('guard_hours'),
                    'kpi_pct'            => $kpiPct,
                    'compliance_pct'     => $compliancePct,
                    'incidents'          => $incidents,
                    'incidents_per_100'  => $this->rate($incidents, (int) $rows->sum('guard_shifts')),
                    'composite_score'    => $parts ? round(array_sum($parts) / count($parts), 1) : null,
                ];
            })
            ->sortByDesc(fn ($c) => $c['composite_score'] ?? -1)
            ->values()
            ->map(fn ($c, $i) => $c + ['rank' => $c['composite_score'] !== null ? $i + 1 : null]);

        return [
            'companies'         => $companies,
            'renewing'          => $companies->where('renewal_flag', true)->pluck('name')->values(),
            'composite_formula' => 'Average of KPI score % and deployment compliance % at the sites the contractor guarded in the period.',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Query building blocks                                              */
    /* ------------------------------------------------------------------ */

    private function incidentBase(): Builder
    {
        return DB::table('incident_occurrences as i')
            ->whereNull('i.deleted_at')
            ->when($this->locationId, fn ($q, $v) => $q->where('i.location_id', $v));
    }

    private function incidentsInRange(): Builder
    {
        return $this->incidentBase()->whereBetween('i.occurred_at', [$this->from, $this->to]);
    }

    private function incidentCount(Carbon $from, Carbon $to): int
    {
        return $this->incidentBase()->whereBetween('i.occurred_at', [$from, $to])->count();
    }

    /** Deployment lines of KPI scorecards (place_id set: target = contracted, scored = deployed). */
    private function deploymentLines(Carbon $from, Carbon $to): Builder
    {
        return DB::table('kpi_scorecard_lines as l')
            ->join('kpi_scorecards as s', 's.id', '=', 'l.kpi_scorecard_id')
            ->whereNull('l.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNotNull('l.place_id')
            ->whereBetween('s.date', [$from->toDateString(), $to->toDateString()])
            ->when($this->locationId, fn ($q, $v) => $q->where('s.location_id', $v));
    }

    private function deploymentTotals(Carbon $from, Carbon $to): array
    {
        $row = $this->deploymentLines($from, $to)
            ->selectRaw('COALESCE(SUM(l.target), 0) AS contracted, COALESCE(SUM(l.scored), 0) AS deployed')
            ->first();

        return $this->complianceRow(['contracted' => $row->contracted, 'deployed' => $row->deployed]);
    }

    private function complianceRow(array $row): array
    {
        $contracted = (int) $row['contracted'];
        $deployed   = (int) $row['deployed'];

        return array_merge($row, [
            'contracted' => $contracted,
            'deployed'   => $deployed,
            'shortfall'  => max($contracted - $deployed, 0),
            'pct'        => $this->pct($deployed, $contracted),
        ]);
    }

    /**
     * KPI scorecards in a window with their KpiScoring breakdown — the same maths as /kpi/reports.
     *
     * @return Collection<int, array{id:int, date:string, location:?string, building_id:?int, breakdown:array}>
     */
    private function scorecardBreakdowns(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $from = $from ?? $this->from;
        $to   = $to ?? $this->to;
        $key  = 'cards:'.$from->toDateString().':'.$to->toDateString();

        return $this->memo[$key] ??= KpiScorecard::with('lines.group')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->when($this->locationId, fn ($q, $v) => $q->where('location_id', $v))
            ->orderBy('date')
            ->get()
            ->map(fn (KpiScorecard $c) => [
                'id'          => $c->id,
                'date'        => $c->date->toDateString(),
                'location'    => $c->location_name,
                'building_id' => $c->building_id,
                'breakdown'   => KpiScoring::breakdown($c),
            ]);
    }

    /**
     * Deployments overlapping the period, clipped to it. Each deployment covers one shift per
     * day, so guard-shifts = guards × days in the period, guard-hours = guard-shifts × shift_hours.
     */
    private function guardShiftRows(): Collection
    {
        return $this->memo['shifts'] ??= DB::table('deployments as d')
            ->join('buildings as b', 'b.id', '=', 'd.building_id')
            ->join('locations as l', 'l.id', '=', 'b.location_id')
            ->join('security_companies as c', 'c.id', '=', 'd.security_company_id')
            ->join('shifts as sh', 'sh.id', '=', 'd.shift_id')
            ->where('d.start_at', '<=', $this->to)
            ->where('d.end_at', '>=', $this->from)
            ->when($this->locationId, fn ($q, $v) => $q->where('b.location_id', $v))
            ->get([
                'd.number_of_guards', 'd.start_at', 'd.end_at', 'd.building_id',
                'l.name as location', 'c.id as company_id', 'c.name as company', 'sh.name as shift',
            ])
            ->map(function ($d) {
                // max()/min() may return the period's own instance, so copy before mutating.
                $start = Carbon::parse($d->start_at)->max($this->from)->copy()->startOfDay();
                $end   = Carbon::parse($d->end_at)->min($this->to)->copy()->startOfDay();
                $days  = (int) $start->diffInDays($end) + 1;
                $gs    = (int) $d->number_of_guards * $days;

                return [
                    'building_id'  => $d->building_id,
                    'location'     => $d->location,
                    'company_id'   => $d->company_id,
                    'company'      => $d->company,
                    'shift'        => $d->shift,
                    'guard_shifts' => $gs,
                    'guard_hours'  => $gs * (int) config('security_analytics.shift_hours', 12),
                ];
            });
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    /** The window of equal length immediately before the current one. */
    private function previousWindow(): array
    {
        $days = (int) $this->from->diffInDays($this->to->copy()->startOfDay()) + 1;

        return [
            $this->from->copy()->subDays($days),
            $this->from->copy()->subSecond(),
        ];
    }

    /**
     * Period buckets for trends: daily up to ~2 months, monthly beyond.
     *
     * @return array{0:string, 1:string, 2:array<string,string>} [granularity, MySQL format, key => label]
     */
    private function periods(): array
    {
        if ($this->from->diffInDays($this->to) <= 62) {
            $periods = [];
            foreach (CarbonPeriod::create($this->from->copy()->startOfDay(), $this->to->copy()->startOfDay()) as $d) {
                $periods[$d->format('Y-m-d')] = $d->format('d M');
            }

            return ['day', '%Y-%m-%d', $periods];
        }

        return ['month', '%Y-%m', $this->monthKeys($this->from, $this->to)];
    }

    /** @return array<string,string> 'Y-m' => 'M Y' */
    private function monthKeys(Carbon $from, Carbon $to): array
    {
        $months = [];
        $cursor = $from->copy()->startOfMonth();
        while ($cursor->lte($to)) {
            $months[$cursor->format('Y-m')] = $cursor->format('M Y');
            $cursor->addMonthNoOverflow();
        }

        return $months;
    }

    private function criticalSeverities(): array
    {
        return array_map('strtolower', (array) config('security_analytics.critical_severities', ['high', 'urgent']));
    }

    private function criticalCaseSql(string $column): string
    {
        $placeholders = implode(',', array_fill(0, count($this->criticalSeverities()), '?'));

        return "SUM(CASE WHEN LOWER({$column}) IN ({$placeholders}) THEN 1 ELSE 0 END)";
    }

    private function avgOverall(Collection $cards): ?float
    {
        return $cards->isEmpty() ? null : round($cards->avg(fn ($c) => $c['breakdown']['overall_pct']), 1);
    }

    private function intCounts(Collection $rows): Collection
    {
        return $rows->map(function ($r) {
            $r->count = (int) $r->count;
            return $r;
        });
    }

    private function pct(int|float $part, int|float $whole): ?float
    {
        return $whole > 0 ? round($part / $whole * 100, 1) : null;
    }

    private function change(int $current, int $previous): ?float
    {
        return $previous > 0 ? round(($current - $previous) / $previous * 100, 1) : null;
    }

    /** Incidents per 100 guard-shifts. */
    private function rate(int $incidents, int $guardShifts): ?float
    {
        return $guardShifts > 0 ? round($incidents / $guardShifts * 100, 2) : null;
    }
}
