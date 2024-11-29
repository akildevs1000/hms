<?php

use App\Http\Controllers\ExternalUrlController;
use Illuminate\Support\Facades\Route;

Route::get('whatsapp-call', [ExternalUrlController::class, 'whatsappCall']);


Route::post('/send-message', [ExternalUrlController::class, 'sendMessage']);
