<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\DailyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Reports\WeeklyController;
use App\Http\Controllers\Reports\MonthlyController;
use App\Http\Controllers\TestController;

Route::get('report', [ReportController::class, 'index']);

//daily
Route::get('/daily', [Controller::class, 'daily']);
Route::get('/daily_download_pdf', [Controller::class, 'daily_download_pdf']);
Route::get('/daily_download_csv', [Controller::class, 'daily_download_csv']);

Route::get('/generateSummaryReport', [DailyController::class, 'generateSummaryReport']);
Route::get('/generatePresentReport', [DailyController::class, 'generatePresentReport']);
Route::get('/generateAbsentReport', [DailyController::class, 'generateAbsentReport']);
Route::get('/generateMissingReport', [DailyController::class, 'generateMissingReport']);
Route::get('/generateManualReport', [DailyController::class, 'generateManualReport']);

// weekly
Route::get('/weekly', [WeeklyController::class, 'weekly']);
Route::get('/weekly_download_pdf', [WeeklyController::class, 'weekly_download_pdf']);
Route::get('/weekly_download_csv', [WeeklyController::class, 'weekly_download_csv']);

//monthly
Route::get('/monthly', [MonthlyController::class, 'monthly']);
Route::get('/monthly_download_pdf', [MonthlyController::class, 'monthly_download_pdf']);
Route::get('/monthly_download_csv', [MonthlyController::class, 'monthly_download_csv']);


//for testing static
Route::get('/daily_html', [Controller::class, 'daily_html']);
Route::get('/weekly_html', [WeeklyController::class, 'weekly_html']);
Route::get('/monthly_html', [MonthlyController::class, 'monthly_html']);




Route::get('/chart_html', [Controller::class, 'chart_html']);
Route::get('/test_chart', [TestController::class, 'index']);

Route::get('/test_week', [TestController::class, 'test_week']);
