<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodPlanController;

Route::resource('foodplan', FoodPlanController::class);
Route::get('foodplan-list', [FoodPlanController::class, "dropDown"]);
Route::get('foodplan-count', [FoodPlanController::class, "getFoodPlanCount"]);

Route::get('booked-foodplans', [FoodPlanController::class, "getBookedFoodPlans"]);
Route::get('foodplan-print', [FoodPlanController::class, "FoodPlanPrint"]);
Route::get('foodplan-download', [FoodPlanController::class, "FoodPlanDownload"]);





