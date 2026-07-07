<?php

namespace App\Filament\Widgets;

use App\Models\MealAttendance;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyMealTodayWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $weekStart = now()
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();

        $weekEnd = now()
            ->startOfWeek(Carbon::MONDAY)
            ->addDays(4)
            ->format('d/m/Y');

        $meal = MealAttendance::query()
            ->where('user_id', auth()->id())
            ->whereDate('week_start', $weekStart)
            ->first();

        $description = 'Semana del '
            . Carbon::parse($weekStart)->format('d/m/Y')
            . ' al '
            . $weekEnd;

        return [
            Stat::make('Desayuno', $meal?->breakfast ? 'Sí' : 'No')
                ->description($description),

            Stat::make('Almuerzo', $meal?->lunch ? 'Sí' : 'No')
                ->description($description),

            Stat::make('Cena', $meal?->dinner ? 'Sí' : 'No')
                ->description($description),
        ];
    }
}