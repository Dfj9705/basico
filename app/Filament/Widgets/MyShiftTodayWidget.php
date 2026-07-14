<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\ShiftAssignment;
use Carbon\Carbon;

class MyShiftTodayWidget extends Widget
{
    // Ocupará todo el ancho de la cuadrícula del dashboard
    protected int|string|array $columnSpan = 'full';

    // Propiedad pública para acceder al turno desde la vista de Blade
    public ?array $todayShift = null;

    public function mount(): void
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Buscar si existe un turno activo hoy para este usuario
        $assignment = ShiftAssignment::with('shiftType')
            ->where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                // El día de hoy debe estar entre la fecha de inicio y fin (si tiene fin)
                $query->where('start_date', '<=', $today)
                    ->where(function ($q) use ($today) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $today);
                });
            })
            ->first();

        if ($assignment) {
            $this->todayShift = [
                'has_shift' => true,
                'name' => $assignment->shiftType->name,
                'frequency' => $assignment->shiftType->frequency === 'weekly' ? 'Semanal' : 'Diario',
                'start_time' => $assignment->shiftType->start_time ? Carbon::parse($assignment->shiftType->start_time)->format('H:i') : null,
                'end_time' => $assignment->shiftType->end_time ? Carbon::parse($assignment->shiftType->end_time)->format('H:i') : null,
                'notes' => $assignment->notes,
            ];
        } else {
            $this->todayShift = [
                'has_shift' => false,
            ];
        }
    }

    protected static string $view = 'filament.widgets.my-shift-today-widget';
}