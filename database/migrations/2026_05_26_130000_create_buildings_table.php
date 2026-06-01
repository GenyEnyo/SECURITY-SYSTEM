<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['name', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
