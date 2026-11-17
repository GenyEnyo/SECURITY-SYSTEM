<?php

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Moves to the new role set: Senior Officer → Security Officer,
 * Supervisor → Location Supervisor, plus the new Data Entry Officer role.
 * Users keep their role through the rename. Permissions are then synced
 * from RoleAndPermissionSeeder.
 */
return new class extends Migration
{
    private array $renames = [
        'Senior Officer' => 'Security Officer',
        'Supervisor'     => 'Location Supervisor',
    ];

    public function up(): void
    {
        foreach ($this->renames as $old => $new) {
            $this->rename($old, $new);
        }

        (new RoleAndPermissionSeeder)->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        foreach (array_flip($this->renames) as $new => $old) {
            $this->rename($new, $old);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function rename(string $from, string $to): void
    {
        $roles = DB::table('roles')->where('guard_name', 'web');

        // Only rename when the target doesn't already exist.
        if ((clone $roles)->where('name', $to)->doesntExist()) {
            (clone $roles)->where('name', $from)->update(['name' => $to, 'updated_at' => now()]);
        }
    }
};
