<?php

use App\Http\Controllers\LostAndFoundItemsController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/lost_and_found_items', LostAndFoundItemsController::class);
Route::post('lost_and_found_items/{key}', [LostAndFoundItemsController::class, 'showRecord']);
Route::post('lost_and_found_items/file_found/{key}', [LostAndFoundItemsController::class, 'uploadFoundImage']);
Route::post('lost_and_found_items/file_returned/{key}', [LostAndFoundItemsController::class, 'uploadReturnedImage']);
Route::post('lost_and_found_items/search_by_reference/{key}', [LostAndFoundItemsController::class, 'searchBookingDetails']);
Route::get('lost_and_found_items/statistics', [LostAndFoundItemsController::class, 'getStaticstics']);


//Route::get('lost_item_details/{key}', [LostAndFoundItemsController::class, 'getLostItemDetails']);
// Route::get('lost_and_found_items/search/{key}', [LostAndFoundItemsController::class, 'search']);
