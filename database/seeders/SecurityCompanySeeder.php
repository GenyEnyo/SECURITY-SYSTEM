<?php

namespace Database\Seeders;

use App\Models\SecurityCompany;
use Illuminate\Database\Seeder;

class SecurityCompanySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['G4S Ghana', 'Royal Knights Security', 'Starry Eagles Ltd.'] as $name) {
            SecurityCompany::firstOrCreate(['name' => $name]);
        }
    }
}
