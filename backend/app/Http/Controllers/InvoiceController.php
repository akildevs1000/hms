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

        // $dompdf = new Dompdf();
        // $dompdf->loadHtml(view('invoice.invoice'));

        // $dompdf->setPaper('A4', 'landscape');


        // return  $dompdf->stream();

        return  Booking::with('bookedRooms')->find($id);

        return Pdf::loadView('invoice.invoice')
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')

            ->stream();
    }
}