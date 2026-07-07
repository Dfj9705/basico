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
            ->leftJoin('grades', 'users.grade_id', '=', 'grades.id')
            ->select('users.*')
            ->with([
                'grade',
                'weaponBranch',
                'mealAttendances' => function ($query) use ($weekStart) {
                    $query->whereDate('week_start', $weekStart);
                },
            ])
            ->orderBy('grades.order')
            ->orderBy('weapon_branches.order')
            ->orderBy('users.catalog_number')
            ->orderBy('users.name')
            ->get();

        $html = view('pdf.meal-report', [
            'records' => $records,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'Letter',
            'orientation' => 'P',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $mpdf->WriteHTML($html);

        $filename = 'reporte-alimentacion-semana-' . $weekStart->format('Y-m-d') . '.pdf';

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}