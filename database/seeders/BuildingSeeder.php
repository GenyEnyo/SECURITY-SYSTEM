<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Location;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            ['name' => 'Head Office',          'location' => 'Accra'],
            ['name' => 'Engineering Building', 'location' => 'Akuse'],
            ['name' => 'Academy',              'location' => 'Akuse'],
            ['name' => 'KTS',                  'location' => 'Tema'],
            ['name' => 'VRA Hospital',         'location' => 'Accra'],
            ['name' => 'Mess Hall',            'location' => 'Aboadze'],
            ['name' => 'Tema Office',          'location' => 'Tema'],
        ];

        foreach ($buildings as $b) {
            $loc = Location::firstOrCreate(['name' => $b['location']]);
            Building::firstOrCreate(['name' => $b['name'], 'location_id' => $loc->id]);
        }
    }
}
