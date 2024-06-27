<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;

Route::resource('template', TemplateController::class);
Route::get('template-list', [TemplateController::class, "dropDown"]);
Route::get('template-types', [TemplateController::class, "templateTypes"]);
