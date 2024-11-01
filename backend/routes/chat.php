<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('chat', [MessageController::class, "index"]);
Route::get('lattest-three-chat', [MessageController::class, "getLattestThreeMessages"]);
Route::post('chat', [MessageController::class, "store"]);
Route::post('update-chat-status', [MessageController::class, "updateChatStatus"]);
Route::get('chat-by-customer-id/{id}', [MessageController::class, "chatByCustomerId"]);
Route::get('/latest-message-count', [MessageController::class, 'getLatestMessageCount']);

Route::get('latest-message-count-customer-id/{id}', [MessageController::class, "getLatestMessageCountByCustomerId"]);


