<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Accra', 'Akuse', 'Aboadze', 'Akosombo', 'Tema'] as $name) {
            Location::firstOrCreate(['name' => $name]);
        }
    }
}
