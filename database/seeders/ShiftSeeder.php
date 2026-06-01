<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Day', 'Night'] as $name) {
            Shift::firstOrCreate(['name' => $name]);
        }
    }
}
