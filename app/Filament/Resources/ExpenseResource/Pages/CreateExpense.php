<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        if (!auth()->user()->hasRole('Administrador')) {
            if ($data['divisible'] == true) {
                $data['force_id'] = null;
            } else {
                $data['force_id'] = auth()->user()->weaponBranch->force_id;
            }
        }
        return $data;
    }
}
