 <?php

    use App\Http\Controllers\BookingController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\RoomController;
    use App\Http\Controllers\RoomTypeController;

    Route::get('room', [RoomController::class, 'index']);
    Route::get('room/search/{key}', [RoomController::class, 'search']);

    Route::get('/get_room/{id}', [RoomController::class, 'getRoom']);


    Route::get('room_type', [RoomTypeController::class, 'index']);
    Route::get('get_data_by_select', [RoomTypeController::class, 'getDataBySelect']);

    Route::get('get_id_cards', [RoomController::class, 'get_id_cards']);


    Route::get('room_list', [RoomController::class, 'roomList']);

    Route::get('room_list_menu', [RoomController::class, 'roomListForMenu']);
    Route::get('get_available_rooms_by_date', [RoomController::class, 'getAvailableRoomsByDate']);
    Route::get('get_food_prices', [RoomController::class, 'getFoodPrices']);

    Route::get('room_list_grid', [RoomController::class, 'roomListForGridView']);


    Route::get('get_room_price_by_meal_plan', [RoomController::class, 'get_room_price_by_meal_plan']);


    Route::post('generate_bill', [BookingController::class, 'generateBill']);
