<?php

namespace App\Filament\Pages;

use App\Models\MealAttendance;
use App\Models\User;
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
            'date' => now()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Día')
                    ->required()
                    ->live(),
            ])
            ->statePath('data');
    }


    public function getRecords()
    {
        return User::query()
            ->with([
                'grade',
                'weaponBranch',
                'mealAttendances' => function ($query) {
                    $query->whereDate('date', $this->data['date'] ?? now());
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
            'meal_report_date' => $this->data['date'] ?? now()->toDateString(),
        ]);

        return redirect()->route('meal-report.pdf');
    }
}