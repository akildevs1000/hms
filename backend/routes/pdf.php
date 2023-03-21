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
