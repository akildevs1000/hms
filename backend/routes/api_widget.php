<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\WidgetApiController;
use Illuminate\Support\Facades\Route;


//Route::apiResource('widget_rooms_availability', WidgetApiController::class);
Route::post('widget_rooms_availability', [WidgetApiController::class, 'getAvailableRoomList']);
//Route::get('widget_rooms_availability', [WidgetApiController::class, 'getAvailableRoomList']);
Route::post('widget_api_room_booking', [WidgetApiController::class, 'widgetRoomBooking']);
//Route::get('widget_rooms_availability2', [WidgetApiController::class, 'getAvailableRoomList2']);
Route::post('widget_api_get_rooms_pricelist', [WidgetApiController::class, 'widgetRoomsListWithPrice']);
