<?php

namespace App\Filament\Widgets;

use App\Models\MealAttendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyMealTodayWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $meal = MealAttendance::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();

        return [
            Stat::make('Desayuno', $meal?->breakfast ? 'Sí' : 'No')
                ->description('Alimentación de hoy'),

            Stat::make('Almuerzo', $meal?->lunch ? 'Sí' : 'No')
                ->description('Alimentación de hoy'),

            Stat::make('Cena', $meal?->dinner ? 'Sí' : 'No')
                ->description('Alimentación de hoy'),
        ];
    }
}