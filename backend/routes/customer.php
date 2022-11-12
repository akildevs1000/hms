 <?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('customer', [CustomerController::class, 'index']);
Route::post('customer', [CustomerController::class, 'store']);


Route::get('customer/search/{key}', [CustomerController::class, 'search']);

Route::get('/get_customer/{contact}', [CustomerController::class, 'getCustomer']);
