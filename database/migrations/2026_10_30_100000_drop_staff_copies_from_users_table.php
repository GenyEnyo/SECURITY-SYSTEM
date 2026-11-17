<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Users only hold what AD gives at login. Department, section and location are
 * read from the staff table, so the copies on users are dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn(['department', 'section']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('staff_number')->constrained('locations')->nullOnDelete();
            $table->string('department')->nullable()->after('location_id');
            $table->string('section')->nullable()->after('department');
        });
    }
};
