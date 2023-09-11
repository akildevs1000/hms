<?php

use App\Http\Controllers\PartyHallController;
use Illuminate\Support\Facades\Route;

Route::post('hall-booking', [PartyHallController::class, 'storeBookingInfo']);
Route::get('hall-booking-details/{key}', [PartyHallController::class, 'getBookingDetails']);
Route::get('hall/upcoming_reservation_list', [PartyHallController::class, 'getBookingUpcomingDetails']);
Route::get('hall/ongoing_reservation_list', [PartyHallController::class, 'getBookingOnGoingDetails']);
Route::get('hall/completed_reservation_list', [PartyHallController::class, 'getBookingCompletedDetails']);
Route::get('hall/get_hall_prices', [PartyHallController::class, 'getHallPriceList']);
