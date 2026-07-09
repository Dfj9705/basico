<?php

namespace App\Filament\Resources\ContributionResource\Pages;

use App\Filament\Resources\ContributionResource;
use App\Models\CashBoxMovement;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContribution extends EditRecord
{
    protected static string $resource = ContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {

        $movement = CashBoxMovement::where('contribution_id', $this->record->id)->first();

        if ($movement) {
            $movement->update([
                'quantity' => $this->record->amount,
            ]);
        }
    }
}
