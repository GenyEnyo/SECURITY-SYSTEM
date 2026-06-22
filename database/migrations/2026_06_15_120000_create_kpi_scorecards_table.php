<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_scorecards', function (Blueprint $table) {
            $table->id();
            // Keep the scorecard if its location/building is later deleted —
            // the *_name snapshots preserve what it was.
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->nullOnDelete();
            $table->string('location_name');
            $table->string('building_name');
            $table->date('date');
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->unique(['location_id', 'building_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_scorecards');
    }
};
