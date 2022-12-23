<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;

Route::resource('expense', ExpenseController::class);
Route::get('expense/search/{key}', [ExpenseController::class, 'search']);

Route::get('account_count', [ExpenseController::class, 'counts']);

Route::resource('payments', PaymentController::class);
