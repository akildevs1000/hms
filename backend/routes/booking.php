 <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\BookingController;

    Route::get('booking', [BookingController::class, 'index']);
    Route::post('store_bulk', [BookingController::class, 'storeBulk']);
    Route::post('booking_validate', [BookingController::class, 'booking_validate']);
    Route::post('booking', [BookingController::class, 'store']);
    Route::get('booking/search/{key}', [BookingController::class, 'search']);


    Route::post('booking1', [BookingController::class, 'store1']);
    Route::post('store_booked_rooms', [BookingController::class, 'storeBookedRooms']);
    Route::post('store_document', [BookingController::class, 'storeDocument']);


    Route::get('events_list', [BookingController::class, 'events_list']);
    Route::get('get_booked_rooms', [BookingController::class, 'getBookedRooms']);
    Route::get('get_events_by_room', [BookingController::class, 'getEventsByRoom']);

    Route::post('update_by_drag', [BookingController::class, 'updateByDrag']);

    Route::get('get_booking', [BookingController::class, 'get_booking']);

    Route::get('reservation_list', [BookingController::class, 'reservationList']);
    Route::get('reservation_list_dash', [BookingController::class, 'reservationListForDash']);

    Route::post('check_in_room', [BookingController::class, 'check_in_room']);
    Route::post('check_out_room', [BookingController::class, 'check_out_room']);


    Route::post('change_room_by_drag', [BookingController::class, 'changeRoomByDrag']);
    Route::post('change_date_by_drag', [BookingController::class, 'changeDateByDrag']);

    Route::post('cancel_room/{id}', [BookingController::class, 'cancelRoom']);
    Route::post('set_available/{id}', [BookingController::class, 'setAvailable']);
    Route::post('set_maintenance/{id}', [BookingController::class, 'setMaintenance']);
    Route::post('paying_advance', [BookingController::class, 'payingAdvance']);
    Route::post('paying_amount', [BookingController::class, 'payingAmount']);