<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FloorController;

Route::resource('floors', FloorController::class);
Route::get('floor-list', [FloorController::class, "dropDown"]);