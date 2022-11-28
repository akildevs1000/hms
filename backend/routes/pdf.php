 <?php

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\CustomerController;
    use App\Http\Controllers\InvoiceController;

    // Route::get('report', [CustomerController::class, 'index']);



    Route::get('invoice', [InvoiceController::class, 'index']);
