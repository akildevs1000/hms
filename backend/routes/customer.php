 <?php

    use App\Http\Controllers\CustomerController;
    use App\Http\Controllers\InquiriesController;
    use App\Http\Controllers\SubCustomerController;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Route;

    Route::get('customer-list', [CustomerController::class, 'dropDown']);
    Route::get('customer/{id}', [CustomerController::class, 'show']);
    Route::get('customer', [CustomerController::class, 'index']);
    Route::post('customer', [CustomerController::class, 'store']);
    Route::post('store_customer', [CustomerController::class, 'storeNewCustomer']);
    Route::post('customer_update', [CustomerController::class, 'update']);


    Route::get('/booking_customers', [CustomerController::class, 'bookingCustomers']);
    Route::get('/booking_customer/{id}', [CustomerController::class, 'viewBookingCustomerBill']);

    Route::get('customer/search/{key}', [CustomerController::class, 'search']);

    Route::get('/get_customer/{contact}', [CustomerController::class, 'getCustomer']);
    Route::get('/get_customer_by_reservation_no/{reservation_no}', [CustomerController::class, 'getCustomerByReservationNo']);
    Route::get('/get_customer_by_id/{id}', [CustomerController::class, 'getCustomerById']);

    Route::get('/get_customer_history/{id}', [CustomerController::class, 'getCustomerHistory']);

    Route::get('/get_countries', function () {
        $countries = DB::table('countries')->get();
        return $countries;
    });



    Route::post('inquiry', [InquiriesController::class, 'store']);
    Route::get('inquiry', [InquiriesController::class, 'index']);
    Route::post('inquiry/{id}', [InquiriesController::class, 'update']);
    Route::get('get_inquiry/{contact_no}', [InquiriesController::class, 'get_inquiry']);

    Route::resource('sub_customer', SubCustomerController::class);
