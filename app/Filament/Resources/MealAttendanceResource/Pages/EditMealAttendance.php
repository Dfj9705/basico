<?php

namespace App\Filament\Resources\MealAttendanceResource\Pages;

use App\Filament\Resources\MealAttendanceResource;
use App\Models\MealAttendance;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMealAttendance extends EditRecord
{
    protected static string $resource = MealAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['week_start'] = $this->record->week_date;

        $data['date'] = $data['week_start'];

        if (!auth()->user()->hasRole('Administrador')) {
            $data['user_id'] = auth()->id();
        }

        $this->validateMealDeadline($data);
        $this->validateExistMealAttendance($data);

        return $data;
    }

    protected function validateMealDeadline(array $data): void
    {
        $weekStart = Carbon::parse($data['week_start']);

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
