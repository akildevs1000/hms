<?php

use App\Http\Controllers\WhatsappClientController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp-client-json', [WhatsappClientController::class, 'store']);
Route::get('/whatsapp-client-json/{company_id}', [WhatsappClientController::class, 'show']);