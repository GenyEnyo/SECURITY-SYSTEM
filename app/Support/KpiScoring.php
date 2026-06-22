<?php

namespace App\Support;

use App\Models\KpiGroup;
use App\Models\KpiScorecard;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for KPI scoring.
 *
 * merit per line = group.weight × (line.scored ÷ Σ targets of that group's lines)
 * group score    = group.weight × (Σ scored ÷ Σ target)   (= Σ of its line merits)
 * overall score  = Σ all line merits  → points out of Σ active weights (100 here)
 *
 * Target-share weighting means a 6-guard gate counts more than a 1-guard post in
 * Deployment; for the quality groups (all targets equal) it reduces to equal weighting.
 */
class KpiScoring
{
    /**
     * Recompute and persist merit for every line on a scorecard. UPDATE only —
     * never deletes rows. Safe to call repeatedly (idempotent).
     */
    public static function recompute(KpiScorecard $record): void
    {
        $record->loadMissing('lines');

        $weights = KpiGroup::pluck('weight', 'id');

        // Σ targets per group, on this scorecard.
        $groupTargets = $record->lines
            ->groupBy('kpi_group_id')
            ->map(fn ($lines) => $lines->sum('target'));

        DB::transaction(function () use ($record, $weights, $groupTargets) {
            foreach ($record->lines as $line) {
                $weight    = (int) ($weights[$line->kpi_group_id] ?? 0);
                $targetSum = (float) ($groupTargets[$line->kpi_group_id] ?? 0);

                $merit = $targetSum > 0
                    ? round($weight * ((float) $line->scored / $targetSum), 1)
                    : 0;

                $line->update(['merit' => $merit]);
            }
        });
    }

    /**
     * Per-group + overall breakdown for views/reports. Does not persist.
     *
     * @return array{groups: array<int, array>, total_points: float, total_weight: int, overall_pct: float}
     */
    public static function breakdown(KpiScorecard $record): array
    {
        $record->loadMissing('lines.group');

        $weights = KpiGroup::pluck('weight', 'id');

        $groups = $record->lines
            ->groupBy('kpi_group_id')
            ->map(function ($lines, $groupId) use ($weights) {
                $weight = (int) ($weights[$groupId] ?? 0);
                $scored = (float) $lines->sum('scored');
                $target = (float) $lines->sum('target');
                $attainment = $target > 0 ? $scored / $target : 0;

                return [
                    'group_id'   => (int) $groupId,
                    'name'       => optional($lines->first()->group)->name ?? 'Unknown',
                    'weight'     => $weight,
                    'scored'     => $scored,
                    'target'     => $target,
                    'attainment' => round($attainment * 100, 1),
                    'points'     => round($weight * $attainment, 1),
                ];
            })
            ->sortBy('group_id')
            ->values()
            ->all();

        $totalPoints = round(array_sum(array_column($groups, 'points')), 1);
        $totalWeight = (int) array_sum(array_column($groups, 'weight'));
        $overallPct  = $totalWeight > 0 ? round($totalPoints / $totalWeight * 100, 1) : 0;

        return [
            'groups'       => $groups,
            'total_points' => $totalPoints,
            'total_weight' => $totalWeight,
            'overall_pct'  => $overallPct,
        ];
    }
}
