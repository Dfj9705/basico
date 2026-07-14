<?php

namespace App\Filament\Resources\ShiftTypeResource\Pages;

use App\Filament\Resources\ShiftTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShiftTypes extends ListRecords
{
    protected static string $resource = ShiftTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
