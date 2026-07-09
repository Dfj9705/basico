<?php

namespace App\Filament\Resources\ContributionResource\Pages;

use App\Filament\Resources\ContributionResource;
use App\Models\CashBoxMovement;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContribution extends CreateRecord
{
    protected static string $resource = ContributionResource::class;

    protected function afterCreate(): void
    {
        $user = User::find($this->record->user_id);
        $forceId = $user->weaponBranch->force_id;
        $quantity = $this->record->amount;
        $type = "ingreso";
        $contributionId = $this->record->id;
        $userId = $this->record->user_id;
        $observation = "Pago de contribución " . $this->record->description . " por el usuario " . $this->record->user->name;

        CashBoxMovement::create([
            'force_id' => $forceId,
            'contribution_id' => $contributionId,
            'quantity' => $quantity,
            'type' => $type,
            'user_id' => $userId,
            'observation' => $observation,
        ]);
    }
}
