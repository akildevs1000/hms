<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostingController;

use App\Http\Controllers\ArController;
use App\Http\Controllers\HotelOrdersFoodController;
use App\Http\Controllers\QrcodeapiController;



Route::get('get_checkin_customer_data', [QrcodeapiController::class, 'getCheckInCustomerDetails']);

Route::get('/hotel_orders_customer_menu', [QrcodeapiController::class, "getCustomerMenu"]);
Route::post('/hotel_orders_add_food_items', [QrcodeapiController::class, "addFoodOrderItems"]);
Route::get('/hotel_orders_get_food_items', [QrcodeapiController::class, "getFoodOrderItems"]);
Route::post('/hotel_orders_cancel_food_item', [QrcodeapiController::class, "cancelFoodOrderItem"]);
