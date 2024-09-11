<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::resource('invoice-v1', InvoiceController::class);
Route::get('invoice-list', [InvoiceController::class, "dropDown"]);
Route::get('invoice-room/{id}', [InvoiceController::class, "roomInvoicePrint"]);
Route::get('invoice-hall/{id}', [InvoiceController::class, "hallInvoicePrint"]);
