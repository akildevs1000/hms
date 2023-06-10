<?php

use App\Http\Controllers\ManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportGenerateController;

Route::post('generate_occupancy_rate', [ManagementController::class, 'generateOccupancyRate']);
Route::get('get_occupancy_rate', [ManagementController::class, 'getOccupancyRate']);
Route::get('get_single_day_occupancy_rate', [ManagementController::class, 'getSingleDayOccupancyRate']);
Route::get('get_occupancy_rate_by_month', [ManagementController::class, 'getOccupancyRateByMonth']);

Route::get('get_source_rate_by_month', [ManagementController::class, 'getSourceRateByMonth']);


Route::get('get_audit_report', [ManagementController::class, 'getAuditReport']);
Route::get('get_occupancy_rate_by_filter', [ManagementController::class, 'getOccupancyRateByFilter']);



// Route::get('get_audit_report_pdf', [ManagementController::class, 'testcheckin']);
Route::get('get_audit_report_pdf', [ReportGenerateController::class, 'generateAuditReport']);
