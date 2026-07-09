<?php

namespace Database\Seeders;

use App\Models\Force;
use App\Models\WeaponBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forces = [
            [
                'name' => 'Fuerza Aérea',
                'is_active' => true,
            ],
            [
                'name' => 'Fuerza de Mar',
                'is_active' => true,
            ],
            [
                'name' => 'Fuerza de Tierra',
                'is_active' => true,
            ],
        ];

        foreach ($forces as $force) {
            Force::firstOrCreate($force);
        }
    }
}
