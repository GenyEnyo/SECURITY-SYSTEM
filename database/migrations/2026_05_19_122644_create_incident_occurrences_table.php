<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incident_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_type_id')->constrained('incident_types')->restrictOnDelete();
            $table->dateTime('occurred_at');
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('severity_id')->constrained('severities')->restrictOnDelete();
            $table->foreignId('incident_status_id')->default(1)->constrained('incident_statuses')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_occurrences');
    }
};
