<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::resource('invoice-v1', InvoiceController::class);
Route::get('invoice-list', [InvoiceController::class, "dropDown"]);
Route::get('invoice-room/{id}', [InvoiceController::class, "roomInvoicePrint"]);
Route::get('invoice-hall/{id}', [InvoiceController::class, "hallInvoicePrint"]);

Route::get('invoice-room-print/{id}', [InvoiceController::class, "roomInvoicePrint"]);
Route::get('invoice-hall-print/{id}', [InvoiceController::class, "hallInvoicePrint"]);

Route::get('invoice-room-pdf/{id}', [InvoiceController::class, "roomInvoicePDF"]);
Route::get('invoice-hall-pdf/{id}', [InvoiceController::class, "hallInvoicePDF"]);
