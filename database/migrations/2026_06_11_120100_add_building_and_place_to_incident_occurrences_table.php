<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_occurrences', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->after('location_id')->constrained('buildings')->nullOnDelete();
            $table->foreignId('place_id')->nullable()->after('building_id')->constrained('places')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incident_occurrences', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropForeign(['place_id']);
            $table->dropColumn(['building_id', 'place_id']);
        });
    }
};
