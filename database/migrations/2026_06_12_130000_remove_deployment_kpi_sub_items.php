<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $group = DB::table('kpi_groups')->where('name', 'Deployment')->first();

        if ($group) {
            // Hard delete — the Deployment beats are now driven by place estimates.
            DB::table('kpi_sub_items')->where('kpi_group_id', $group->id)->delete();
        }
    }

    public function down(): void
    {
        // Irreversible — the legacy Deployment sub-items are intentionally removed.
    }
};
