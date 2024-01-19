<?php

use App\Http\Controllers\HotelFoodCategoriesController;
use App\Http\Controllers\HotelFoodItemsController;
use App\Http\Controllers\HotelFoodTimingsController;
use App\Http\Controllers\HotelOrdersFoodController;
use App\Models\HotelFoodTimings;
use Illuminate\Support\Facades\Route;
//Categories
Route::apiResource('/hotel_food_categories', HotelFoodCategoriesController::class);
Route::get('/get_hotel_menu_categories_list', [HotelFoodCategoriesController::class, "getHotelMenuCategoriesList"]);
Route::post('/menu_food_item_categories_display_order_update', [HotelFoodCategoriesController::class, "updateMenuDisplayOrder"]);

//items
Route::apiResource('/hotel_food_items', HotelFoodItemsController::class);
Route::get('/hotel_food_dropdown_list', [HotelFoodItemsController::class, "hotelFoodDrodownList"]);
Route::post('/menu_food_items_display_order_update', [HotelFoodItemsController::class, "updateMenuDisplayOrder"]);




Route::apiResource('/hotel_food_timings', HotelFoodTimingsController::class);
Route::get('/get_hotel_menu_timings_list', [HotelFoodTimingsController::class, "getHotelMenuTimingsList"]);
Route::post('/menu_timings_display_order_update', [HotelFoodTimingsController::class, "updateMenuDisplayOrder"]);

//hotel orders
Route::apiResource('/hotel_orders_food', HotelOrdersFoodController::class);
//Route::get('/get_hotel_menu_timings_list', [HotelFoodTimingsController::class, "getHotelMenuTimingsList"]);

Route::post('/hotel_order_status_to_preparing', [HotelOrdersFoodController::class, "updateHotelOrderToPreparing"]);
Route::post('/hotel_order_status_to_delivered', [HotelOrdersFoodController::class, "updateHotelOrderToDelivered"]);
Route::post('/hotel_order_status_to_cancel', [HotelOrdersFoodController::class, "updateHotelOrderToCancel"]);
Route::get('/hotel_orders_statistics', [HotelOrdersFoodController::class, "getDashbordStatistics"]);
//Route::get('/hotel_orders_customer_menu', [HotelOrdersFoodController::class, "getCustomerMenu"]);
