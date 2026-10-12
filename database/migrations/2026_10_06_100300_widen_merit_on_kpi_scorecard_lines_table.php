<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Merit is kept to 4 decimals so line merits add up to the group weight
    // (3 × 3.3333 = 10.0, not 3 × 3.3 = 9.9). Views round to 1 decimal.
    public function up(): void
    {
        Schema::table('kpi_scorecard_lines', function (Blueprint $table) {
            $table->decimal('merit', 8, 4)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kpi_scorecard_lines', function (Blueprint $table) {
            $table->decimal('merit', 6, 1)->nullable()->change();
        });
    }
};
