<?php

namespace App\Filament\Resources\MealAttendanceResource\Pages;

use App\Filament\Resources\MealAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMealAttendances extends ListRecords
{
    protected static string $resource = MealAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    
}
