 <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\BookingController;
    use App\Http\Controllers\PostingController;

    Route::get('booking', [BookingController::class, 'index']);
    Route::post('booking_validate', [BookingController::class, 'booking_validate']);
    Route::post('booking', [BookingController::class, 'store']);
    Route::get('booking/search/{key}', [BookingController::class, 'search']);

    Route::get('events_list', [BookingController::class, 'events_list']);

    Route::post('update_by_drag', [BookingController::class, 'updateByDrag']);

    Route::get('get_booking_by_check_in', [BookingController::class, 'get_booking_by_check_in']);

    Route::post('check_in_room', [BookingController::class, 'check_in_room']);
    Route::post('check_out_room', [BookingController::class, 'check_out_room']);

    Route::post('cancel_reservation/{id}', [BookingController::class, 'cancelReservation']);
    Route::post('set_available/{id}', [BookingController::class, 'setAvailable']);
    Route::post('paying_advance', [BookingController::class, 'payingAdvance']);

    Route::resource('posting', PostingController::class);