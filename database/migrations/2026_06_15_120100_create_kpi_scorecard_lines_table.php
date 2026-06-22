<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_scorecard_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_scorecard_id')->constrained('kpi_scorecards')->cascadeOnDelete();
            $table->foreignId('kpi_group_id')->constrained('kpi_groups')->restrictOnDelete();
            // Standard sub-item rows carry kpi_sub_item_id; deployment beats carry place_id.
            $table->foreignId('kpi_sub_item_id')->nullable()->constrained('kpi_sub_items')->nullOnDelete();
            $table->foreignId('place_id')->nullable()->constrained('places')->nullOnDelete();
            // Snapshots so reports stay stable even if config is later edited/deleted.
            $table->string('criteria');
            $table->unsignedInteger('target')->nullable();
            $table->unsignedInteger('scored')->nullable();
            $table->decimal('merit', 6, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_scorecard_lines');
    }
};
