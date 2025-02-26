<?php

use App\Http\Controllers\ExternalUrlController;
use Illuminate\Support\Facades\Route;

Route::get('whatsapp-call', [ExternalUrlController::class, 'whatsappCall']);

Route::post('/send-message', [ExternalUrlController::class, 'sendMessage']);

Route::get('/test-email-pdf', [ExternalUrlController::class, 'testPdf']);

Route::get('/sandbox/{id}', [ExternalUrlController::class, 'sandBox']);

Route::get('/get_last_whatsapp_client_id/{id}',[ExternalUrlController::class, 'getLastWhatsappClientId']);
