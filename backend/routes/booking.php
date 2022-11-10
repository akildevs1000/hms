 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('booking', [BookingController::class, 'index']);
Route::post('booking', [BookingController::class, 'store']);
Route::get('booking/search/{key}', [BookingController::class, 'search']);
