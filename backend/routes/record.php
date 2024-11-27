<?php

use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Route;

Route::get('summary-report', [RecordController::class, 'summaryReport']);
Route::get('cash-report', [RecordController::class, 'cashReport']);
Route::get('ota-report', [RecordController::class, 'otaReport']);
Route::get('ota-trn', [RecordController::class, 'otaTRN']);

