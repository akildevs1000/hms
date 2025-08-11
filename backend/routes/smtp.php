<?php

use App\Http\Controllers\SMTPController;
use Illuminate\Support\Facades\Route;

Route::post('/smtp', [SMTPController::class, 'storeOrUpdate']);
Route::get('/smtp', [SMTPController::class, 'show']);
