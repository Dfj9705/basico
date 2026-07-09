<?php

namespace App\Filament\Resources\ForceResource\Pages;

use App\Filament\Resources\ForceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForces extends ListRecords
{
    protected static string $resource = ForceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
