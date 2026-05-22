<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('severities', function (Blueprint $table) {
            $table->string('color', 7)->default('#6b7280')->after('name');
        });

        foreach ([
            'Low'    => '#1f7a45',
            'Medium' => '#b07000',
            'High'   => '#b22216',
            'Urgent' => '#fc3320',
        ] as $name => $hex) {
            DB::table('severities')->where('name', $name)->update(['color' => $hex]);
        }
    }

    public function down(): void
    {
        Schema::table('severities', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
