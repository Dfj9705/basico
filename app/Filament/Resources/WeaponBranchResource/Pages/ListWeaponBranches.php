<?php

namespace App\Filament\Resources\WeaponBranchResource\Pages;

use App\Filament\Resources\WeaponBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeaponBranches extends ListRecords
{
    protected static string $resource = WeaponBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
