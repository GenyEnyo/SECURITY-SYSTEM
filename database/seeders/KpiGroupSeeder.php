<?php

namespace Database\Seeders;

use App\Models\KpiGroup;
use Illuminate\Database\Seeder;

class KpiGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Edit this array with your real KPI groups + sub-items, then run:
        //   php artisan db:seed --class=KpiGroupSeeder
        // firstOrCreate makes this safe to re-run after edits — existing rows
        // (matched by name) are left alone; only new entries are inserted.
        // Label fields (criteria_label / target_label) are force-applied on
        // every run so re-seeding propagates label changes onto existing rows.
        $groups = [
            [
                'name'   => 'Bearing and App',
                'weight' => 10,
                'items'  => [
                    ['criteria' => 'Turnout',  'target' => 50,],
                    ['criteria' => 'Neatness', 'target' => 50,],
                    ['criteria' => 'Equipped', 'target' => 50,],
                ],
            ],
            [
                'name'   => 'Prof Capabilities',
                'weight' => 10,
               'items'  => [
                    ['criteria' => 'Training',  'target' => 50,],
                    ['criteria' => 'Skill', 'target' => 50,],
                    ['criteria' => 'Language Proficiency', 'target' => 50,],
                    ['criteria' => 'Alertness', 'target' => 50,],
                    ['criteria' => 'Fitness (physically)', 'target' => 50,],

                ],
            ],

            [
                'name'   => 'Performance Duties',
                'weight' => 10,
               'items'  => [
                    ['criteria' => 'Sentery Duties',  'target' => 50,],
                    ['criteria' => 'Guard Patrols', 'target' => 50,],
                    ['criteria' => 'Punctuality', 'target' => 50,],
                    ['criteria' => 'Duty Log Book Records', 'target' => 50,],

                ],
            ],

               [
                'name'           => 'Reliability',
                'weight'         => 10,
                'criteria_label' => 'Attributes',
               'items'  => [
                    ['criteria' => 'Integrity', 'target' => 50,],
                    ['criteria' => 'Dedication to Duty', 'target' => 50,],
                    ['criteria' => 'Trustworthy', 'target' => 50,],
                    ['criteria' => 'Politeness', 'target' => 50,],

                ],
            ],

            [
                // Deployment beats are now driven by per-place guard estimates
                // (places.estimated_guards), not hand-entered sub-items.
                'name'           => 'Deployment',
                'weight'         => 60,
                'criteria_label' => 'Beats',
                'target_label'   => 'Estimated',
                'items'          => [],
            ],
        ];

        foreach ($groups as $g) {
            $group = KpiGroup::firstOrCreate(['name' => $g['name']], ['weight' => $g['weight']]);

            $group->forceFill([
                'criteria_label' => $g['criteria_label'] ?? 'Criteria',
                'target_label'   => $g['target_label']   ?? 'Target',
            ])->save();

            foreach ($g['items'] as $i) {
                $group->subItems()->firstOrCreate(['criteria' => $i['criteria']], $i);
            }
        }
    }
}
