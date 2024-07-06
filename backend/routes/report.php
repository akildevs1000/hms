<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('report-by-source', [ReportController::class, 'reportBySource']);
Route::get('report-by-top-ten-customer', [ReportController::class, 'getReportTopTenCustomers']);

