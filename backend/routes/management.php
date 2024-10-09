<?php

use App\Http\Controllers\AuditHistoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\ReportGenerateController;
use Illuminate\Support\Facades\Route;

Route::post('generate_occupancy_rate', [ManagementController::class, 'generateOccupancyRate']);
Route::get('get_occupancy_rate', [ManagementController::class, 'getOccupancyRate']);
Route::get('get_single_day_occupancy_rate', [ManagementController::class, 'getSingleDayOccupancyRate']);
Route::get('get_occupancy_rate_by_month', [ManagementController::class, 'getOccupancyRateByMonth']);
Route::get('get_occupancy_rate_by_month2', [ManagementController::class, 'getOccupancyRateByMonth2']);

Route::get('get_source_rate_by_month', [ManagementController::class, 'getSourceRateByMonth']);

// Route::get('get_audit_report', [ManagementController::class, 'getAuditReport']);
Route::get('get_audit_report', [AuditHistoryController::class, 'getAuditReport']);
Route::get('get_occupancy_rate_by_filter', [ManagementController::class, 'getOccupancyRateByFilter']);

// Route::get('get_audit_report_pdf', [ManagementController::class, 'testcheckin']);
Route::get('get_audit_report_pdf', [ReportGenerateController::class, 'generateAuditReport']);

Route::get('get_report_monthly_wise', [ManagementController::class, 'getReportMonthlyWise']);
Route::get('get_report_monthly_wise_group', [ManagementController::class, 'getReportMonthlyWiseGroup']);
Route::get('get_report_monthly_wise_print', [ManagementController::class, 'getReportMonthlyWiseGroupPrint']);
Route::get('get_report_top_ten_customers', [ManagementController::class, 'getReportTop10Customers']);
Route::get('get_report_daily_wise_group', [ManagementController::class, 'getReportDailyWiseGroup']);

Route::get('get_re_calculate_price/{key}', [BookingController::class, 'reCalculatePrice']);

Route::get('reCalculatePriceTest', [BookingController::class, 'reCalculatePriceTest']);
