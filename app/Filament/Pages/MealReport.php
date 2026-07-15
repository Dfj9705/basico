<?php

namespace App\Filament\Pages;

use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Services\MealReportExcelService;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

                        $start = now()->startOfWeek(Carbon::MONDAY);
                        $end = now()->addMonths(6)->startOfWeek(Carbon::MONDAY);

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

    private function getSelectedWeekStart(): Carbon
    {
        return Carbon::parse(
            $this->data['week_start']
            ?? now()
                ->startOfWeek(Carbon::MONDAY)
                ->addWeek()
                ->toDateString()
        )->startOfWeek(Carbon::MONDAY);
    }
    public function getRecords()
    {
        $weekStart = $this->getSelectedWeekStart()
            ->toDateString();

        return User::query()
            ->leftJoin(
                'weapon_branches',
                'users.weapon_branch_id',
                '=',
                'weapon_branches.id'
            )
            ->select('users.*')
            ->with([
                'grade',
                'weaponBranch',
                'mealAttendances' => function ($query) use ($weekStart) {
                    $query->where(
                        'week_start',
                        $weekStart
                    );
                },
            ])
            ->orderBy('weapon_branches.order')
            ->orderBy('users.catalog_number')
            ->orderBy('users.name')
            ->get();
    }

    public function exportPdf()
    {
        session([
            'meal_report_week_start' => $this->data['week_start'] ?? now()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ]);

        return redirect()->route('meal-report.pdf');
    }

    public function exportExcel(
        MealReportExcelService $excelService
    ): StreamedResponse {
        $weekStart = $this->getSelectedWeekStart();

        $users = $this->getRecords();

        $writer = $excelService->generate(
            weekStart: $weekStart,
            users: $users,
        );

        $weekEnd = $weekStart
            ->copy()
            ->addDays(4);

        $fileName = sprintf(
            'nomina-alimentacion-%s-al-%s.xlsx',
            $weekStart->format('Y-m-d'),
            $weekEnd->format('Y-m-d')
        );

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            $fileName,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                'Cache-Control' =>
                    'max-age=0, no-cache, no-store, must-revalidate',

                'Pragma' => 'public',
            ]
        );
    }

    public function exportExcelSummary(
        MealReportExcelService $excelService
    ): StreamedResponse {
        $weekStart = $this->getSelectedWeekStart();

        $users = $this->getRecords();

        $writer = $excelService->generateSummary(
            weekStart: $weekStart,
            users: $users,
        );

        $weekEnd = $weekStart
            ->copy()
            ->addDays(4);

        $fileName = sprintf(
            'nomina-alimentacion-%s-al-%s.xlsx',
            $weekStart->format('Y-m-d'),
            $weekEnd->format('Y-m-d')
        );

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            $fileName,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                'Cache-Control' =>
                    'max-age=0, no-cache, no-store, must-revalidate',

                'Pragma' => 'public',
            ]
        );
    }

}