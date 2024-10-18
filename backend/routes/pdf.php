 <?php

  use App\Http\Controllers\CustomerController;
  use App\Http\Controllers\GRCController;
  use App\Http\Controllers\ReportController;
  use App\Http\Controllers\ReportGenerateController;
  use Illuminate\Support\Facades\Route;

  // Route::get('report', [CustomerController::class, 'index']);

  Route::get('invoice/{id}/{inv?}', [GRCController::class, 'index'])->name('pdf.invoice');

  Route::get('checkin_report_print', [ReportController::class, 'CHeckInReport']);
  Route::get('checkin_report_download', [ReportController::class, 'CHeckInReportDownload']);
  Route::get('inhouse_report_print', [ReportController::class, 'InHouseReport']);
  Route::get('foodorder_report_print', [ReportController::class, 'FoodOrderReport']);

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
  Route::get('gst_invoice_report_csv_download', [ReportController::class, 'gstInvoiceReportCSVDownload']);

  Route::get('income_report_print', [ReportController::class, 'incomingReport']);
  Route::get('income_report_download', [ReportController::class, 'incomingReportDownload']);

  Route::get('expense_report_print', [ReportController::class, 'expenseReport']);
  Route::get('expense_report_download', [ReportController::class, 'expenseReportDownload']);

  Route::get('reservation_report_print', [ReportController::class, 'reservationReport']);
  Route::get('reservation_report_download', [ReportController::class, 'reservationReportDownload']);

  Route::get('grc/{id}', [GRCController::class, 'grc']);
  Route::get('grc_by_checkin/{id}', [GRCController::class, 'grcByCheckin']);
  Route::get('grc_report_print/{id}', [GRCController::class, 'grcPrint']);
  Route::get('grc_report_download/{id}', [GRCController::class, 'grcDownload']);
  Route::get('download_customer_attachments/{id}', [GRCController::class, 'downloadCustomerAttachments']);

  Route::get('revenue_monthly_report_print', [ReportController::class, 'revenueMonthlyReportPrint']);
  Route::get('revenue_monthly_report_download', [ReportController::class, 'revenueMonthlyReportDownload']);

  Route::get('revenue_daily_report_print', [ReportController::class, 'revenueDailyReportPrint']);
  Route::get('revenue_daily_report_download', [ReportController::class, 'revenueDailyReportDownload']);

  Route::get('revenue_customer_wise_report_print', [ReportController::class, 'revenueCustomerWiseReportPrint']);
  Route::get('revenue_customer_wise_report_download', [ReportController::class, 'revenueCustomerWiseReportDownload']);
  Route::get('statement-print/{id}/{statementType}/{from}/{to}', [CustomerController::class, 'statementPrint']);
  Route::get('statement-download/{id}/{statementType}/{from}/{to}', [CustomerController::class, 'statementDownload']);


  Route::get('html-test', [ReportController::class, 'htmlTest']);
  Route::get('generate-night-audit-report/{id}/{data}', [ReportGenerateController::class, 'processData']);
