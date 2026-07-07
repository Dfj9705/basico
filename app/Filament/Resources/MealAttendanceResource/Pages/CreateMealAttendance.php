<?php

namespace App\Filament\Resources\MealAttendanceResource\Pages;

use App\Filament\Resources\MealAttendanceResource;
use App\Models\MealAttendance;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMealAttendance extends CreateRecord
{
    protected static string $resource = MealAttendanceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateMealDeadline($data);
        $this->validateExistMealAttendance($data);

        if (!auth()->user()->hasRole('Administrador')) {
            $data['user_id'] = auth()->id();
        }
        return $data;
    }
    protected function validateMealDeadline(array $data): void
    {
        $mealDate = Carbon::parse($data['date']);

        $limitDate = $mealDate
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->subDays(4)          // Jueves de la semana anterior
            ->setTime(12, 0, 0);  // 12:00:00 p.m.

        if (now()->greaterThan($limitDate)) {
            Notification::make()
                ->title('Error')
                ->body('El plazo para registrar comidas de esa semana finalizó el jueves anterior a las 12:00 p.m.')
                ->danger()
                ->send();

            $this->halt();
        }

    }

    protected function validateExistMealAttendance(array $data): void
    {
        if (!auth()->user()->hasRole('Administrador')) {
            $existingAttendance = MealAttendance::where('user_id', auth()->id())
                ->where('date', $data['date'])
                ->first();

            if ($existingAttendance) {
                Notification::make()
                    ->title('Error')
                    ->body('Ya existe un registro de comidas para esa fecha.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        } else {
            $existingAttendance = MealAttendance::where('user_id', $data['user_id'])
                ->where('date', $data['date'])
                ->first();

            if ($existingAttendance) {
                Notification::make()
                    ->title('Error')
                    ->body('Ya existe un registro de comidas para esa fecha.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }
    }
}
