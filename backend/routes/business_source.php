<?php

use App\Http\Controllers\BusinessSourceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('business-source', BusinessSourceController::class);
Route::get('business-source-list', [BusinessSourceController::class, "dropDown"]);
