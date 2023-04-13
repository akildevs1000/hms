<?php

use App\Http\Controllers\TestController;
use App\Mail\DbBackupMail;
use App\Models\Agent;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Food;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::post('booking_validate1', [TestController::class, 'booking_validate']);
Route::post('store_test', [TestController::class, 'store']);

Route::get('/test', function (Request $request) {

    return Hash::make('Hyders@2001');
    return Hash::make('Hyders@1901');
    return Hash::make('Hyders@1801');
    return Hash::make('Hyders@1701');

});

Route::get('/db_backup', function (Request $request) {

    // $data = [
    //     'file' => collect(glob(storage_path("app/ezhms/*.zip")))->last(),
    //     'date' => date('Y-M-d H:i'),
    //     'body' => 'ezhms Database Backup',
    // ];

    // return Mail::to(env("ADMIN_MAIL_RECEIVERS"))->queue(new DbBackupMail($data));

    $data = [
        'file' => collect(glob(storage_path("app/ezhms/*.zip")))->last(),
        'date' => date('Y-M-d H:i'),
        'body' => 'ezhms Database Backup',
    ];

    Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new DbBackupMail($data));
});

Route::get('remove_booking/{id}', function ($id) {

    Booking::find($id)->delete();
    Payment::whereBookingId($id)->delete();
    Transaction::whereBookingId($id)->delete();
    BookedRoom::whereBookingId($id)->delete();
    OrderRoom::whereBookingId($id)->delete();
    CancelRoom::whereBookingId($id)->delete();
    Food::whereBookingId($id)->delete();

    return "removed booking";
});

Route::get('remove_booking_all', function () {

    foreach (Booking::get() as $booking) {
        $id = $booking->id;
        Payment::whereBookingId($id)->delete();
        Transaction::whereBookingId($id)->delete();
        BookedRoom::whereBookingId($id)->delete();
        OrderRoom::whereBookingId($id)->delete();
        CancelRoom::whereBookingId($id)->delete();
        Food::whereBookingId($id)->delete();
        Agent::whereBookingId($id)->delete();
    }
    Booking::truncate();
    return "removed booking";
});
