<?php

namespace App\Filament\Resources\MealAttendanceResource\Pages;

use App\Filament\Resources\MealAttendanceResource;
use App\Models\MealAttendance;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMealAttendance extends CreateRecord
{
    protected static string $resource = MealAttendanceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $weekStart = Carbon::parse($this->form->getState()['week_start'])
            ->startOfWeek(Carbon::MONDAY);

        $this->validateMealDeadline($weekStart);

        $userId = auth()->user()->hasRole('Administrador')
            ? $data['user_id']
            : auth()->id();

        $firstRecord = null;

        for ($day = $weekStart->copy(); $day->lte($weekStart->copy()->addDays(4)); $day->addDay()) {
            $record = MealAttendance::updateOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $day->toDateString(),
                ],
                [
                    'breakfast' => $data['breakfast'] ?? false,
                    'lunch' => $data['lunch'] ?? false,
                    'dinner' => $data['dinner'] ?? false,
                ]
            );

            $firstRecord ??= $record;
        }

        Notification::make()
            ->title('Semana registrada')
            ->body('Se guardó la alimentación de lunes a viernes.')
            ->success()
            ->send();

        return $firstRecord;
    }

    protected function validateMealDeadline(Carbon $weekStart): void
    {
        $limitDate = $weekStart
            ->copy()
            ->subDays(5)
            ->setTime(15, 0, 0);

        if (now()->greaterThan($limitDate)) {
            Notification::make()
                ->title('Error')
                ->body('El plazo para registrar comidas de esa semana finalizó el miércoles anterior a las 15:00 p.m.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}