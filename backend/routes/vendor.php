<?php

use App\Http\Controllers\VendorCategoryController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('vendor-category', VendorCategoryController::class);
Route::get('vendor-category-list', [VendorCategoryController::class, "dropDown"]);
Route::apiResource('vendor', VendorController::class);
Route::get('vendor-list', [VendorController::class, "dropDown"]);
