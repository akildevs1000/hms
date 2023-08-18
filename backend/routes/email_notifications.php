<?php

use App\Http\Controllers\EmailNotificationsController;
use App\Http\Controllers\NotificationReportTypesController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/email_notifications', EmailNotificationsController::class);
Route::post('/email_notifications/storeSingle', [EmailNotificationsController::class, 'storeSingle']);

Route::apiResource('/notification_report_Types', NotificationReportTypesController::class);
