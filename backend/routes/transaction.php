<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('transaction', [TransactionController::class, 'store']);
Route::get('get_transaction_by_booking_id/{id}', [TransactionController::class, 'getTransactionByBookingId']);

Route::get('get_transaction_by_users', [TransactionController::class, 'getTransactionByUsers']);

Route::post('remove_single_transaction', [TransactionController::class, 'removeSingleTransaction']);
