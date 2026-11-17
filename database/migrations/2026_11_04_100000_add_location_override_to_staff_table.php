<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An admin's location correction for a staff member. Used instead of the HR
 * location until HR changes that person's location (see StaffDirectory::sync).
 * staff:sync never writes these columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('location_name')->constrained('locations')->nullOnDelete();
            $table->string('location_hr_name_at_override')->nullable()->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn('location_hr_name_at_override');
        });
    }
};
