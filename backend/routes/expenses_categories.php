<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ExpensesCategoriesController;
use App\Http\Controllers\TaxSlabsController;
use App\Http\Controllers\VendorsController;
use App\Models\TaxSlabs;
use App\Models\Vendors;
use Illuminate\Support\Facades\Route;

Route::apiResource('/expenses_categories', ExpensesCategoriesController::class);
Route::apiResource('/vendors', VendorsController::class);
Route::get('/vendors_dropdown_list', [VendorsController::class, 'vendorsDropdownList']);
