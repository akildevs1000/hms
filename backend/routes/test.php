<?php

use App\Http\Controllers\TestController;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Expense;
use App\Models\Food;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\Taxable;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('booking_validate1', [TestController::class, 'booking_validate']);
Route::post('store_test', [TestController::class, 'store']);

Route::get('/test', function (Request $request) {
    // return date('Y-m-D H:i');
});

Route::get('remove_booking/{id}', function ($id) {

    return Hash::make($id);
    return;

    Booking::find($id)->delete();
    Payment::whereBookingId($id)->delete();
    Transaction::whereBookingId($id)->delete();
    BookedRoom::whereBookingId($id)->delete();
    OrderRoom::whereBookingId($id)->delete();
    CancelRoom::whereBookingId($id)->delete();
    Food::whereBookingId($id)->delete();

    return "removed booking";
});

Route::get('truncate', function () {

    // return now();
    Booking::truncate();
    Payment::truncate();
    Posting::truncate();
    Transaction::truncate();
    BookedRoom::truncate();
    OrderRoom::truncate();
    CancelRoom::truncate();
    Food::truncate();
    Taxable::truncate();
    Expense::truncate();
    // Customer::truncate();
    return "truncate done";
});
