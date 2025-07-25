<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('profit-loss', [PaymentController::class,"ProfitLoss"]);
Route::get('payments', [PaymentController::class,"Payments"]);

Route::get('payments-for-report', [PaymentController::class,"PaymentsForReport"]);


