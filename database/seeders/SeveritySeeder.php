<?php

namespace Database\Seeders;

use App\Models\Severity;
use Illuminate\Database\Seeder;

class SeveritySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'Low'    => '#1f7a45',
            'Medium' => '#b07000',
            'High'   => '#b22216',
            'Urgent' => '#fc3320',
        ];

        foreach ($defaults as $name => $color) {
            Severity::firstOrCreate(['name' => $name], ['color' => $color]);
        }
    }
}
