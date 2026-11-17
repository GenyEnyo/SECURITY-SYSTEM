<?php

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

/** Adds the "Manage Staff Locations" permission and re-syncs role permissions. */
return new class extends Migration
{
    public function up(): void
    {
        (new RoleAndPermissionSeeder)->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
