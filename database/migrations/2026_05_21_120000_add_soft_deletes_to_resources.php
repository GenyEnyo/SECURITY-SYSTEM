<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incident_occurrences', fn (Blueprint $t) => $t->softDeletes());
        Schema::table('incident_types',       fn (Blueprint $t) => $t->softDeletes());
        Schema::table('severities',           fn (Blueprint $t) => $t->softDeletes());
    }

    public function down(): void
    {
        Schema::table('incident_occurrences', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('incident_types',       fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('severities',           fn (Blueprint $t) => $t->dropSoftDeletes());
    }
};
