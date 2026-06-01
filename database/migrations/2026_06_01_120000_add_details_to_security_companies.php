<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('security_companies', function (Blueprint $table) {
            $table->string('contact')->nullable()->after('name');
            $table->string('contract_detail')->nullable()->after('contact');
            $table->string('status')->default('active')->after('contract_detail');
        });
    }

    public function down(): void
    {
        Schema::table('security_companies', function (Blueprint $table) {
            $table->dropColumn(['contact', 'contract_detail', 'status']);
        });
    }
};
