<?php

use App\Http\Controllers\EmailNotificationsController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/email_notifications', EmailNotificationsController::class);
Route::post('/email_notifications/storeSingle', [EmailNotificationsController::class, 'storeSingle']);
