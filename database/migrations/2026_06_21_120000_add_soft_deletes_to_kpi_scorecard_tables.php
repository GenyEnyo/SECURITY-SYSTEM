<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_scorecards', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('kpi_scorecard_lines', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('kpi_scorecards', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('kpi_scorecard_lines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
