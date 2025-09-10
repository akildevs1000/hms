<?php

use App\Http\Controllers\TransactionNumberSeriesController;
use Illuminate\Support\Facades\Route;

Route::get('transaction_number_series', [TransactionNumberSeriesController::class, "show"]);
Route::post('transaction_number_series', [TransactionNumberSeriesController::class, "storeOrUpdate"]);


