<?php

namespace App\Console\Commands;

use App\Models\KpiScorecard;
use App\Support\KpiScoring;
use Illuminate\Console\Command;

class BackfillKpiMerit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:backfill-merit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompute system-generated merit for every saved scorecard (UPDATE only, never deletes).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cards = KpiScorecard::with('lines')->get();

        if ($cards->isEmpty()) {
            $this->info('No scorecards found — nothing to backfill.');

            return self::SUCCESS;
        }

        $this->withProgressBar($cards, function (KpiScorecard $card) {
            KpiScoring::recompute($card);
        });

        $this->newLine(2);
        $this->info("Recomputed merit for {$cards->count()} scorecard(s).");

        return self::SUCCESS;
    }
}
