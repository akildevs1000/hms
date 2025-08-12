 <?php

    use App\Http\Controllers\ChatMessagesController;
    use Illuminate\Support\Facades\Route;


    Route::resource('chat_messages', ChatMessagesController::class);


    // Route::get('get-posting-by-booking-id-and-room-id', [ChatMessagesController::class, 'getPostingByBookingIdAncRoomId']);
    // Route::get('get-posting-by-booking-id-and-room-id-groupbydate', [ChatMessagesController::class, 'getPostingByBookingIdAncRoomIdDate']);
