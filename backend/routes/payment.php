<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('profit-loss', [PaymentController::class,"ProfitLoss"]);
