<?php

namespace Database\Seeders;

use App\Models\IncidentType;
use Illuminate\Database\Seeder;

class IncidentTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Break in', 'Theft', 'Injury', 'Damage', 'Item Loss', 'Other'] as $name) {
            IncidentType::firstOrCreate(['name' => $name]);
        }
    }
}
