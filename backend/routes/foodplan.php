<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodPlanController;

Route::resource('foodplan', FoodPlanController::class);
Route::get('foodplan-list', [FoodPlanController::class, "dropDown"]);
