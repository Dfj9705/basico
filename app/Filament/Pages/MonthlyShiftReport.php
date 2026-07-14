<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Models\ShiftAssignment;
use Carbon\Carbon;
use Mpdf\Mpdf;

class MonthlyShiftReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $title = 'Reporte Mensual de Turnos';
    protected static ?string $navigationGroup = 'Reportes';

    public ?array $data = [];
    protected static string $view = 'filament.pages.monthly-shift-report';

    public function mount(): void
    {
        $this->form->fill([
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('month')
                    ->label('Seleccionar Mes')
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre'
                    ])
                    ->required()
                    ->native(false),

                Select::make('year')
                    ->label('Seleccionar Año')
                    ->options(array_combine(
                        range(Carbon::now()->year - 2, Carbon::now()->year + 3),
                        range(Carbon::now()->year - 2, Carbon::now()->year + 3)
                    ))
                    ->required()
                    ->native(false),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Descargar PDF (Calendario)')
                ->color('primary')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn() => $this->exportPdf()),
        ];
    }

    public function exportPdf()
    {
        $formData = $this->form->getState();
        $month = $formData['month'];
        $year = $formData['year'];

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // 1. Obtener todas las asignaciones del rango con sus usuarios y tipos de turno
        $assignments = ShiftAssignment::with(['user', 'shiftType'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereNull('end_date')
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->get();

        $daysInMonth = $startDate->daysInMonth;
        $startOfWeekDay = $startDate->dayOfWeek; // 0 = Domingo, 1 = Lunes, etc.

        // 2. Agrupar asignaciones por el día del mes: matrix[día][] = [usuario, turno, color]
        $matrix = array_fill(1, $daysInMonth, []);

        foreach ($assignments as $assignment) {
            $assignStart = Carbon::parse($assignment->start_date);
            $assignEnd = $assignment->end_date ? Carbon::parse($assignment->end_date) : $endDate;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDayDate = Carbon::createFromDate($year, $month, $day);

                if ($currentDayDate->between($assignStart, $assignEnd)) {
                    $matrix[$day][] = [
                        'userName' => $assignment->user->name,
                        'shiftName' => $assignment->shiftType->name,
                        'frequency' => $assignment->shiftType->frequency,
                        'color' => $assignment->shiftType->frequency === 'weekly' ? '#9333ea' : '#2563eb', // Morado para semanal, Azul para diario
                    ];
                }
            }
        }

        // 3. Renderizar el HTML de la vista
        $html = view('pdf.monthly-shifts', [
            'matrix' => $matrix,
            'monthName' => $startDate->translatedFormat('F'),
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'startOfWeekDay' => $startOfWeekDay,
        ])->render();

        // 4. Configurar mPDF en horizontal
        $mpdf = new mPDF([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print ($mpdf->Output('', 'S')),
            "Calendario_Grupal_Turnos_{$year}_{$month}.pdf"
        );
    }
}