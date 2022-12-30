<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::post('transaction', [TransactionController::class, 'store']);