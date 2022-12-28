 <?php

    use App\Http\Controllers\CustomerController;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Route;

    Route::get('customer', [CustomerController::class, 'index']);
    Route::post('customer', [CustomerController::class, 'store']);
    Route::post('customer_update', [CustomerController::class, 'update']);


    Route::get('/booking_customers', [CustomerController::class, 'bookingCustomers']);
    Route::get('/booking_customer/{id}', [CustomerController::class, 'viewBookingCustomerBill']);

    Route::get('customer/search/{key}', [CustomerController::class, 'search']);

    Route::get('/get_customer/{contact}', [CustomerController::class, 'getCustomer']);

    Route::get('/get_customer_history/{id}', [CustomerController::class, 'getCustomerHistory']);

    Route::get('/get_countries', function () {
        $countries = DB::table('countries')->get();
        return $countries;
    });