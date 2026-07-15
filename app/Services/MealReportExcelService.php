<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\PageMargins;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MealReportExcelService
{
    private const TITLE =
        'NÓMINA DEL PERSONAL DEL CURSO BÁSICO PROMOCIÓN LXVIII';

    private const HEADER_ROW = 5;

    private const FIRST_DATA_ROW = 6;

    public function generate(
        Carbon $weekStart,
        Collection $users
    ): Xlsx {
        $weekStart = $weekStart
            ->copy()
            ->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart
            ->copy()
            ->addDays(4);

        $spreadsheet = new Spreadsheet();

        /*
         * PhpSpreadsheet crea una hoja automáticamente.
         * La reutilizamos para desayuno.
         */
        $breakfastSheet = $spreadsheet->getActiveSheet();
        $breakfastSheet->setTitle('DESAYUNO');

        $this->buildSheet(
            sheet: $breakfastSheet,
            action: 'DESAYUNAR',
            users: $this->filterUsersByMeal($users, 'breakfast'),
            weekStart: $weekStart,
            weekEnd: $weekEnd,
        );

        $lunchSheet = new Worksheet(
            $spreadsheet,
            'ALMUERZO'
        );

        $spreadsheet->addSheet($lunchSheet);

        $this->buildSheet(
            sheet: $lunchSheet,
            action: 'ALMORZAR',
            users: $this->filterUsersByMeal($users, 'lunch'),
            weekStart: $weekStart,
            weekEnd: $weekEnd,
        );

        $dinnerSheet = new Worksheet(
            $spreadsheet,
            'CENA'
        );

        $spreadsheet->addSheet($dinnerSheet);

        $this->buildSheet(
            sheet: $dinnerSheet,
            action: 'CENAR',
            users: $this->filterUsersByMeal($users, 'dinner'),
            weekStart: $weekStart,
            weekEnd: $weekEnd,
        );

        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Reporte semanal de alimentación')
            ->setSubject('Nómina de alimentación')
            ->setDescription(
                'Reporte semanal de desayuno, almuerzo y cena.'
            );

        return new Xlsx($spreadsheet);
    }


    public function generateSummary(
        Carbon $weekStart,
        Collection $users
    ): Xlsx {
        $weekStart = $weekStart
            ->copy()
            ->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart
            ->copy()
            ->addDays(4);

        $spreadsheet = new Spreadsheet();

        /*
         * PhpSpreadsheet crea una hoja automáticamente.
         * La reutilizamos para desayuno.
         */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CURSO BASICO');

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue(
            'A1',
            'BASICO PROMOCION LXVIII					'
        );

        $sheet->getStyle('A1:F1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:F1')
            ->getFont()
            ->setName('Arial')
            ->setBold(true);

        $sheet->setCellValue(
            'A2',
            'NO.'
        );

        $sheet->setCellValue(
            'B2',
            'GRADO'
        );

        $sheet->setCellValue(
            'C2',
            'NOMBRE'
        );

        $sheet->setCellValue(
            'D2',
            'DESAYUNO'
        );

        $sheet->setCellValue(
            'E2',
            'ALMUERZO'
        );

        $sheet->setCellValue(
            'F2',
            'CENA'
        );

        $sheet->getStyle('A2:F2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A2:F2')
            ->getFont()
            ->setName('Arial')
            ->setBold(true);

        $sheet->getColumnDimension('A')
            ->setWidth(4);

        $sheet->getColumnDimension('B')
            ->setWidth(15);

        $sheet->getColumnDimension('C')
            ->setWidth(30);

        $sheet->getColumnDimension('D')
            ->setWidth(15);

        $sheet->getColumnDimension('E')
            ->setWidth(15);

        $sheet->getColumnDimension('F')
            ->setWidth(15);

        $count = 3;
        foreach ($users as $user) {
            $sheet->setCellValue(
                'A' . $count,
                $count
            );

            $sheet->setCellValue(
                'B' . $count,
                $user->grade->name
            );

            $sheet->setCellValue(
                'C' . $count,
                $user->name
            );

            $sheet->setCellValue(
                'D' . $count,
                $user->mealAttendances->first()?->breakfast
                ? '✓'
                : ''
            );

            $sheet->setCellValue(
                'E' . $count,
                $user->mealAttendances->first()?->lunch
                ? '✓'
                : ''
            );

            $sheet->setCellValue(
                'F' . $count,
                $user->mealAttendances->first()?->dinner
                ? '✓'
                : ''
            );

            $count++;
        }

        $spreadsheet->setActiveSheetIndex(0);



        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Reporte semanal de alimentación')
            ->setSubject('Nómina de alimentación')
            ->setDescription(
                'Reporte semanal de desayuno, almuerzo y cena.'
            );

        return new Xlsx($spreadsheet);
    }

    private function filterUsersByMeal(
        Collection $users,
        string $meal
    ): Collection {
        return $users
            ->filter(function ($user) use ($meal): bool {
                $attendance = $user
                    ->mealAttendances
                    ->first();

                return (bool) ($attendance?->{$meal} ?? false);
            })
            ->values();
    }

    private function buildSheet(
        Worksheet $sheet,
        string $action,
        Collection $users,
        Carbon $weekStart,
        Carbon $weekEnd
    ): void {
        $lastDataRow = self::FIRST_DATA_ROW
            + max($users->count(), 1)
            - 1;

        $this->configureColumns($sheet);
        $this->configureRows($sheet);
        $this->writeTitles(
            $sheet,
            $action,
            $weekStart,
            $weekEnd
        );

        $this->writeHeaders($sheet);
        $this->writeUsers($sheet, $users);
        $this->applyTableStyle(
            $sheet,
            $lastDataRow
        );

        $this->configurePrint(
            $sheet,
            $lastDataRow
        );
    }

    private function configureColumns(
        Worksheet $sheet
    ): void {
        /*
         * Se conservan columnas vacías A y E,
         * igual que en el archivo de referencia.
         */
        $sheet->getColumnDimension('A')
            ->setWidth(4);

        $sheet->getColumnDimension('B')
            ->setWidth(8);

        $sheet->getColumnDimension('C')
            ->setWidth(28);

        $sheet->getColumnDimension('D')
            ->setWidth(48);

        $sheet->getColumnDimension('E')
            ->setWidth(4);
    }

    private function configureRows(
        Worksheet $sheet
    ): void {
        $sheet->getRowDimension(1)
            ->setRowHeight(24);

        $sheet->getRowDimension(2)
            ->setRowHeight(22);

        $sheet->getRowDimension(3)
            ->setRowHeight(10);

        $sheet->getRowDimension(4)
            ->setRowHeight(10);

        $sheet->getRowDimension(5)
            ->setRowHeight(22);
    }

    private function writeTitles(
        Worksheet $sheet,
        string $action,
        Carbon $weekStart,
        Carbon $weekEnd
    ): void {
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        $sheet->setCellValue(
            'A1',
            self::TITLE
        );

        $sheet->setCellValue(
            'A2',
            sprintf(
                'QUE PASARÁN A %s %s',
                $action,
                $this->dateRangeText(
                    $weekStart,
                    $weekEnd
                )
            )
        );

        $sheet->getStyle('A1:E2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:E2')
            ->getFont()
            ->setName('Arial')
            ->setBold(true);

        $sheet->getStyle('A1')
            ->getFont()
            ->setSize(12);

        $sheet->getStyle('A2')
            ->getFont()
            ->setSize(11);
    }

    private function writeHeaders(
        Worksheet $sheet
    ): void {
        $sheet->setCellValue('B5', 'No.');
        $sheet->setCellValue('C5', 'Grado');
        $sheet->setCellValue(
            'D5',
            'Nombres y Apellidos'
        );

        $sheet->getStyle('B5:D5')
            ->getFont()
            ->setName('Arial')
            ->setSize(11)
            ->setBold(true);

        $sheet->getStyle('B5:D5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function writeUsers(
        Worksheet $sheet,
        Collection $users
    ): void {
        if ($users->isEmpty()) {
            $row = self::FIRST_DATA_ROW;

            $sheet->mergeCells("B{$row}:D{$row}");

            $sheet->setCellValue(
                "B{$row}",
                'NO HAY PERSONAL REGISTRADO'
            );

            $sheet->getStyle("B{$row}:D{$row}")
                ->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                )
                ->setVertical(
                    Alignment::VERTICAL_CENTER
                );

            $sheet->getStyle("B{$row}:D{$row}")
                ->getFont()
                ->setItalic(true);

            $sheet->getRowDimension($row)
                ->setRowHeight(22);

            return;
        }

        foreach ($users as $index => $user) {
            $row = self::FIRST_DATA_ROW + $index;

            $sheet->setCellValue(
                "B{$row}",
                $index + 1
            );

            $sheet->setCellValue(
                "C{$row}",
                $user->grade?->name ?? '-'
            );

            $sheet->setCellValue(
                "D{$row}",
                $user->name
            );

            $sheet->getRowDimension($row)
                ->setRowHeight(21);
        }
    }

    private function applyTableStyle(
        Worksheet $sheet,
        int $lastDataRow
    ): void {
        $range = "B5:D{$lastDataRow}";

        $sheet->getStyle($range)
            ->getFont()
            ->setName('Arial')
            ->setSize(10);

        $sheet->getStyle($range)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setARGB('FF000000');

        $sheet->getStyle("B6:B{$lastDataRow}")
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        $sheet->getStyle("C6:C{$lastDataRow}")
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_LEFT
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        $sheet->getStyle("D6:D{$lastDataRow}")
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_LEFT
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            )
            ->setWrapText(true);

        $sheet->getStyle('B5:D5')
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setARGB('FFE7E6E6');

        $sheet->getStyle("B5:D{$lastDataRow}")
            ->getAlignment()
            ->setWrapText(true);
    }

    private function configurePrint(
        Worksheet $sheet,
        int $lastDataRow
    ): void {
        $sheet->setShowGridlines(false);

        $sheet->getPageSetup()
            ->setOrientation(
                PageSetup::ORIENTATION_PORTRAIT
            );

        $sheet->getPageSetup()
            ->setPaperSize(
                PageSetup::PAPERSIZE_LETTER
            );

        $sheet->getPageSetup()
            ->setFitToWidth(1);

        $sheet->getPageSetup()
            ->setFitToHeight(0);

        $sheet->getPageSetup()
            ->setRowsToRepeatAtTopByStartAndEnd(
                1,
                5
            );

        $sheet->getPageSetup()
            ->setPrintArea(
                "A1:E{$lastDataRow}"
            );

        $sheet->getPageMargins()
            ->setTop(0.35)
            ->setRight(0.35)
            ->setBottom(0.35)
            ->setLeft(0.35)
            ->setHeader(0.15)
            ->setFooter(0.15);

        $sheet->getHeaderFooter()
            ->setOddFooter(
                '&C Página &P de &N'
            );

        $sheet->getSheetView()
            ->setZoomScale(90);
    }

    private function dateRangeText(
        Carbon $weekStart,
        Carbon $weekEnd
    ): string {
        Carbon::setLocale('es');

        if (
            $weekStart->month === $weekEnd->month
            && $weekStart->year === $weekEnd->year
        ) {
            return sprintf(
                'DEL %s AL %s DE %s DE %s',
                $weekStart->format('d'),
                $weekEnd->format('d'),
                mb_strtoupper(
                    $weekEnd->translatedFormat('F')
                ),
                $weekEnd->format('Y')
            );
        }

        if ($weekStart->year === $weekEnd->year) {
            return sprintf(
                'DEL %s DE %s AL %s DE %s DE %s',
                $weekStart->format('d'),
                mb_strtoupper(
                    $weekStart->translatedFormat('F')
                ),
                $weekEnd->format('d'),
                mb_strtoupper(
                    $weekEnd->translatedFormat('F')
                ),
                $weekEnd->format('Y')
            );
        }

        return sprintf(
            'DEL %s DE %s DE %s AL %s DE %s DE %s',
            $weekStart->format('d'),
            mb_strtoupper(
                $weekStart->translatedFormat('F')
            ),
            $weekStart->format('Y'),
            $weekEnd->format('d'),
            mb_strtoupper(
                $weekEnd->translatedFormat('F')
            ),
            $weekEnd->format('Y')
        );
    }
}