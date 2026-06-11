<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            IncidentStatusSeeder::class,
            IncidentTypeSeeder::class,
            LocationSeeder::class,
            BuildingSeeder::class,
            SecurityCompanySeeder::class,
            ShiftSeeder::class,
            SeveritySeeder::class,
            KpiGroupSeeder::class,
            UserSeeder::class,
        ]);
    }
}
