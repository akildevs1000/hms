 <?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('report', [CustomerController::class, 'index']);