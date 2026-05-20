<?php

namespace Database\Seeders;

use App\Models\Severity;
use Illuminate\Database\Seeder;

class SeveritySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Low', 'Medium', 'High', 'Urgent'] as $name) {
            Severity::firstOrCreate(['name' => $name]);
        }
    }
}
