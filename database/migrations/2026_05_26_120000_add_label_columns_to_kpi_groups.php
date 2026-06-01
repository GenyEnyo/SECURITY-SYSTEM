<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_groups', function (Blueprint $table) {
            $table->string('criteria_label')->default('Criteria')->after('weight');
            $table->string('target_label')->default('Target')->after('criteria_label');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_groups', function (Blueprint $table) {
            $table->dropColumn(['criteria_label', 'target_label']);
        });
    }
};
