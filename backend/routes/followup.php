<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FollowupController;

Route::resource('followup', FollowupController::class);
