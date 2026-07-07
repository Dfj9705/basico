<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MealReportPdfController;

Route::get('/reportes/alimentacion/pdf', [MealReportPdfController::class, 'download'])
    ->name('meal-report.pdf')
    ->middleware(['auth']);