<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignUserRole extends Command
{
    protected $signature = 'user:assign-role {username : Username or email of the user} {role : Role name, e.g. Super-Admin}';

    protected $description = 'Assign a role to a user (use it to grant the first admin after an LDAP login)';

    public function handle(): int
    {
        $login = $this->argument('username');
        $user = User::where('username', $login)->orWhere('email', $login)->first();

        if (! $user) {
            $this->error("No user found for [{$login}]. LDAP users must sign in once before they exist locally.");

            return self::FAILURE;
        }

        $role = Role::where('name', $this->argument('role'))->where('guard_name', 'web')->first();

        if (! $role) {
            $this->error('Unknown role. Available roles: '.Role::pluck('name')->implode(', '));

            return self::FAILURE;
        }

        $user->assignRole($role);

        $this->info("{$user->name} now has the {$role->name} role.");

        return self::SUCCESS;
    }
}
