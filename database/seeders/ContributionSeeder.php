<?php

namespace Database\Seeders;

use App\Models\CashBoxMovement;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $contribution = Contribution::create([
                'amount' => 1500,
                'contribution_date' => now(),
                'user_id' => $user->id,
                'description' => 'Primer pago',
            ]);

            $movement = CashBoxMovement::create([
                'force_id' => $user->weaponBranch->force_id,
                'contribution_id' => $contribution->id,
                'quantity' => 1500,
                'type' => 'ingreso',

            ]);


        }
    }
}
