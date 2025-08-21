 <?php

    use App\Http\Controllers\ChatMessagesController;
    use Illuminate\Support\Facades\Route;


    Route::resource('chat_messages', ChatMessagesController::class);
    Route::get('chat_messages_history', [ChatMessagesController::class, 'getChatHistory']);
    Route::get('chat_messages_bookings', [ChatMessagesController::class, 'getChatBookingsList']);


    Route::post('chat_messages_upload_file', [ChatMessagesController::class, 'getChatUploadFile']);
    Route::get('chat_download_image', [ChatMessagesController::class, 'downloadChatImage']);

    Route::get('chat_get_unread_messages', [ChatMessagesController::class, 'getChatReceiptionUnreadMessages']);
    Route::get('chat_get_guest_unread_messages', [ChatMessagesController::class, 'getChatGuestUnreadMessages']);

    Route::post('chat_update_agent_read_status', [ChatMessagesController::class, 'updateAgentReadStatus']);
    Route::get('chat_get_unread_messages_group_by_bookingid', [ChatMessagesController::class, 'getChatUnreadMessagesGroupByBookingId']);



    Route::post('chat_update_guest_read_status', [ChatMessagesController::class, 'updateGuestReadStatus']);














    // Route::get('get-posting-by-booking-id-and-room-id', [ChatMessagesController::class, 'getPostingByBookingIdAncRoomId']);
    // Route::get('get-posting-by-booking-id-and-room-id-groupbydate', [ChatMessagesController::class, 'getPostingByBookingIdAncRoomIdDate']);
