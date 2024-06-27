<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::resource('invoice', InvoiceController::class);
Route::get('invoice-list', [InvoiceController::class, "dropDown"]);
