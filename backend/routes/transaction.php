<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::post('transaction', [TransactionController::class, 'store']);
Route::get('get_transaction_by_booking_id/{id}', [TransactionController::class, 'getTransactionByBookingId']);