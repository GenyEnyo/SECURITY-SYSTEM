<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Local account used to sign in when LDAP_ENABLED=false.
        $user = User::firstOrCreate(
            ['email' => 'kwasi@example.com'],
            ['name' => 'Kwasi Ansah', 'username' => 'kwasi', 'password' => Hash::make('password')],
        );

        if (! $user->username) {
            $user->update(['username' => 'kwasi']);
        }

        $user->assignRole('Super-Admin');
    }
}
