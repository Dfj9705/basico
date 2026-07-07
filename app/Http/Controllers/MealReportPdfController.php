<?php

namespace App\Http\Controllers;

use App\Models\MealAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class MealReportPdfController extends Controller
{
    public function download(Request $request)
    {
        $date = session('meal_report_date', now()->toDateString());

        $records = User::query()
            ->with([
                'grade',
                'weaponBranch',
                'mealAttendances' => function ($query) use ($date) {
                    $query->whereDate('date', $date);
                }
            ])
            ->orderBy('catalog_number')
            ->orderBy('grade_id')
            ->orderBy('weapon_branch_id')
            ->orderBy('name')
            ->get();

        $html = view('pdf.meal-report', [
            'records' => $records,
            'date' => $date,
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

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="reporte-alimentacion-' . $date . '.pdf"');
    }
}