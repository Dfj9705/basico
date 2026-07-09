<?php

namespace App\Filament\Resources\ForceResource\Pages;

use App\Filament\Resources\ForceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForce extends EditRecord
{
    protected static string $resource = ForceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
