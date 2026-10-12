<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->nullable()->after('shift_id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('expected_guards')->nullable()->after('number_of_guards');
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supervisor_id');
            $table->dropColumn('expected_guards');
        });
    }
};
