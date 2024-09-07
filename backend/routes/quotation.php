<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;

Route::resource('quotation', QuotationController::class);
Route::get('quotation-list', [QuotationController::class, "dropDown"]);
Route::get('quotation-room/{id}', [QuotationController::class, "roomQuotaionPrint"]);
Route::get('quotation-hall/{id}', [QuotationController::class, "hallQuotaionPrint"]);

