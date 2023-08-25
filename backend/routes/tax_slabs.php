<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\TaxSlabsController;
use App\Models\TaxSlabs;
use Illuminate\Support\Facades\Route;

Route::apiResource('/tax_slabs', TaxSlabsController::class);
Route::get('getTaxSlab', [BookingController::class, 'getTaxSlab']);
