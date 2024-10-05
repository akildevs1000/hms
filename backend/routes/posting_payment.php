<?php

use App\Http\Controllers\PostingPaymentController;
use Illuminate\Support\Facades\Route;

Route::post('posting-payment', [PostingPaymentController::class, 'store']);
