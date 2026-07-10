<?php

namespace App\Filament\Resources\CashBoxMovementResource\Pages;

use App\Filament\Resources\CashBoxMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashBoxMovement extends EditRecord
{
    protected static string $resource = CashBoxMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
