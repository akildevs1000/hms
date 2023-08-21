<?php

use App\Http\Controllers\WidgetApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('widget_rooms_availability', WidgetApiController::class);
Route::get('widget_rooms_availability', [WidgetApiController::class, 'getAvailableRoomList']);
