<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::resource('invoice-v1', InvoiceController::class);
Route::get('invoice-list', [InvoiceController::class, "dropDown"]);
