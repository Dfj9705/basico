<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class MealReportPdfController extends Controller
{
    public function download(Request $request)
    {
        $weekStart = Carbon::parse(
            session('meal_report_week_start', now()->startOfWeek(Carbon::MONDAY)->toDateString())
        )->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(4);

        $records = User::query()
            ->with([
                'grade',
                'weaponBranch',
                'mealAttendances' => function ($query) use ($weekStart, $weekEnd) {
                    $query->whereBetween('date', [
                        $weekStart->toDateString(),
                        $weekEnd->toDateString(),
                    ]);
                },
            ])
            ->orderBy('catalog_number')
            ->orderBy('grade_id')
            ->orderBy('weapon_branch_id')
            ->orderBy('name')
            ->get();

        $html = view('pdf.meal-report', [
            'records' => $records,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'Letter',
            'orientation' => 'L',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 8,
            'margin_right' => 8,
        ]);

        $mpdf->WriteHTML($html);

        $filename = 'reporte-alimentacion-semana-' . $weekStart->format('Y-m-d') . '.pdf';

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}