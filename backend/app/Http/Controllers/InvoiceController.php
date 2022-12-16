<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;

class InvoiceController extends Controller
{

    public function index($id)
    {

        $booking = Booking::with('bookedRooms', 'customer')->find($id);
        $bookedRooms = $booking->bookedRooms;


        return Pdf::loadView('invoice.invoice', compact("booking", "bookedRooms"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')

            ->stream();
    }
}