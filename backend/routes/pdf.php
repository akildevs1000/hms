 <?php

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\CustomerController;
    use App\Http\Controllers\InvoiceController;
    use App\Http\Controllers\ReportController;

    // Route::get('report', [CustomerController::class, 'index']);


    Route::get('invoice/{id}', [InvoiceController::class, 'index'])->name('pdf.invoice');
    Route::get('grc/{id}', [InvoiceController::class, 'grc']);

    Route::get('checkin_report_print', [ReportController::class, 'CHeckInReport']);
    Route::get('checkin_report_download', [ReportController::class, 'CHeckInReportDownload']);

    Route::get('checkout_report_print', [ReportController::class, 'CHeckOutReport']);
    Route::get('checkout_report_download', [ReportController::class, 'CHeckOutReportDownload']);

    Route::get('available_rooms_print', [ReportController::class, 'availableRoomsReport']);
    Route::get('available_rooms_download', [ReportController::class, 'availableRoomsReportDownload']);

    Route::get('booked_rooms_print', [ReportController::class, 'bookedRoomsReport']);
    Route::get('booked_rooms_download', [ReportController::class, 'bookedRoomsReportDownload']);


    Route::get('paid_report_print', [ReportController::class, 'paidRoomsReport']);
    Route::get('paid_report_download', [ReportController::class, 'paidRoomsReportDownload']);

    Route::get('dirty_report_print', [ReportController::class, 'dirtyRoomsReport']);
    Route::get('dirty_report_download', [ReportController::class, 'dirtyRoomsReportDownload']);
