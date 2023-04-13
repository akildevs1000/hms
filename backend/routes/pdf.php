 <?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Route::get('report', [CustomerController::class, 'index']);

Route::get('invoice/{id}/{inv?}', [InvoiceController::class, 'index'])->name('pdf.invoice');
Route::get('grc/{id}', [InvoiceController::class, 'grc']);

Route::get('checkin_report_print', [ReportController::class, 'CHeckInReport']);
Route::get('checkin_report_download', [ReportController::class, 'CHeckInReportDownload']);

Route::get('checkout_report_print', [ReportController::class, 'CHeckOutReport']);
Route::get('checkout_report_download', [ReportController::class, 'CHeckOutReportDownload']);

Route::get('expect_checkin_report_print', [ReportController::class, 'expectCHeckInReport']);
Route::get('expect_checkin_report_download', [ReportController::class, 'expectCHeckInReportDownload']);

Route::get('expect_checkout_report_print', [ReportController::class, 'expectCHeckOutReport']);
Route::get('expect_checkout_report_download', [ReportController::class, 'expectCHeckOutReportDownload']);

Route::get('available_rooms_print', [ReportController::class, 'availableRoomsReport']);
Route::get('available_rooms_download', [ReportController::class, 'availableRoomsReportDownload']);

Route::get('booked_rooms_print', [ReportController::class, 'bookedRoomsReport']);
Route::get('booked_rooms_download', [ReportController::class, 'bookedRoomsReportDownload']);

Route::get('paid_report_print', [ReportController::class, 'paidRoomsReport']);
Route::get('paid_report_download', [ReportController::class, 'paidRoomsReportDownload']);

Route::get('dirty_report_print', [ReportController::class, 'dirtyRoomsReport']);
Route::get('dirty_report_download', [ReportController::class, 'dirtyRoomsReportDownload']);

Route::get('gst_invoice_report_print', [ReportController::class, 'gstInvoiceReport']);
Route::get('gst_invoice_report_download', [ReportController::class, 'gstInvoiceReportDownload']);

Route::get('income_report_print', [ReportController::class, 'incomingReport']);
Route::get('income_report_download', [ReportController::class, 'incomingReportDownload']);

Route::get('expense_report_print', [ReportController::class, 'expenseReport']);
Route::get('expense_report_download', [ReportController::class, 'expenseReportDownload']);

Route::get('reservation_report_print', [ReportController::class, 'reservationReport']);
Route::get('reservation_report_download', [ReportController::class, 'reservationReportDownload']);

Route::get('grc_by_checkin/{id}', [InvoiceController::class, 'grcByCheckin']);

Route::get('grc_report_print/{id}', [InvoiceController::class, 'grcPrint']);
Route::get('grc_report_download/{id}', [InvoiceController::class, 'grcDownload']);
