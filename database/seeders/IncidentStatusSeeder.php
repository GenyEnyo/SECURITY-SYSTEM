<?php

namespace Database\Seeders;

use App\Models\IncidentStatus;
use Illuminate\Database\Seeder;

class IncidentStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Reported', 'Reviewing', 'Resolved', 'Escalated', 'Closed'] as $name) {
            IncidentStatus::firstOrCreate(['name' => $name]);
        }
    }
}
