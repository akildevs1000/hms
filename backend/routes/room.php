 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;

Route::get('room', [RoomController::class, 'index']);
Route::get('room/search/{key}', [RoomController::class, 'search']);
