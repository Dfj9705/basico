<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\WeaponBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grade::firstOrCreate([
            'name' => 'Teniente',
        ]);
        Grade::firstOrCreate([
            'name' => 'Alférez de Navío',
        ]);

        WeaponBranch::firstOrCreate([
            'name' => 'Infantería',
        ]);
        WeaponBranch::firstOrCreate([
            'name' => 'Artillería',
        ]);

        WeaponBranch::firstOrCreate([
            'name' => 'Marina',
        ]);
        WeaponBranch::firstOrCreate([
            'name' => 'Aviación',
        ]);
        WeaponBranch::firstOrCreate([
            'name' => 'Intendencia',
        ]);
    }
}
