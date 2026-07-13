<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\User;
use App\Models\WeaponBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = Grade::pluck('id')->toArray();
        $branches = WeaponBranch::pluck('id')->toArray();

        if (empty($grades) || empty($branches)) {
            $this->command->error('Primero ejecute los seeders de grados y armas.');
            return;
        }

        User::factory()
            ->count(50)
            ->sequence(fn($sequence) => [
                'catalog_number' => 100000 + $sequence->index,
                'grade_id' => fake()->randomElement($grades),
                'weapon_branch_id' => fake()->randomElement($branches),
            ])
            ->create()
        ;
    }
}
