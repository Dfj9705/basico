<?php

namespace App\Filament\Pages;

use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class MealReport extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Alimentación';
    protected static ?string $title = 'Reporte de Alimentación';

    protected static string $view = 'filament.pages.meal-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'week_start' => now()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('week_start')
                    ->label('Semana')
                    ->options(function () {
                        $weeks = [];

                        $start = now()->subMonths(2)->startOfWeek(Carbon::MONDAY);
                        $end = now()->addMonths(2)->startOfWeek(Carbon::MONDAY);

                        while ($start->lte($end)) {
                            $monday = $start->copy();
                            $friday = $start->copy()->addDays(4);

                            $weeks[$monday->toDateString()] = 'Semana del '
                                . $monday->format('d/m/Y')
                                . ' al '
                                . $friday->format('d/m/Y');

                            $start->addWeek();
                        }

                        return $weeks;
                    })
                    ->searchable()
                    ->required()
                    ->live(),
            ])
            ->statePath('data');
    }

    public function getRecords()
    {
        $weekStart = Carbon::parse($this->data['week_start'] ?? now())
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();

        return User::query()
            ->with([
                'grade',
                'weaponBranch',
                'mealAttendances' => function ($query) use ($weekStart) {
                    $query->whereDate('week_start', $weekStart);
                },
            ])
            ->orderBy('catalog_number')
            ->orderBy('grade_id')
            ->orderBy('weapon_branch_id')
            ->orderBy('name')
            ->get();
    }

    public function exportPdf()
    {
        session([
            'meal_report_week_start' => $this->data['week_start'] ?? now()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ]);

        return redirect()->route('meal-report.pdf');
    }
}