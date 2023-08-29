<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ExpensesCategoriesController;
use App\Http\Controllers\TaxSlabsController;
use App\Models\TaxSlabs;
use Illuminate\Support\Facades\Route;

Route::apiResource('/expenses_categories', ExpensesCategoriesController::class);
