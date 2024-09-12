<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;

Route::post('/update-telegram-chat-id/{id}', [TelegramController::class, 'updateTelegramChatId']);
Route::get('/generate-telegram-otp/{id}', [TelegramController::class, 'generateOtp']);
Route::get('/validate-telegram-otp/{id}', [TelegramController::class, 'validateOtp']);


