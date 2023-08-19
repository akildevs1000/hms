<?php

use App\Http\Controllers\WordpressApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('wordpress_rooms_availability', WordpressApiController::class);
Route::get('wordpress_rooms_availability', [WordpressApiController::class, 'getAvailableRoomList']);
