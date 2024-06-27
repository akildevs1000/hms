<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;

Route::resource('quotation', QuotationController::class);
Route::get('quotation-list', [QuotationController::class, "dropDown"]);
