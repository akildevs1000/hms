 <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\RoomController;
    use App\Http\Controllers\RoomTypeController;

    Route::get('room', [RoomController::class, 'index']);
    Route::get('room/search/{key}', [RoomController::class, 'search']);

    Route::get('/get_room/{id}', [RoomController::class, 'getRoom']);


    Route::get('room_type', [RoomTypeController::class, 'index']);
    Route::get('get_id_cards', [RoomController::class, 'get_id_cards']);