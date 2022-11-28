<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {

        // $dompdf = new Dompdf();
        // $dompdf->loadHtml(view('invoice.invoice'));

        // $dompdf->setPaper('A4', 'landscape');


        // return  $dompdf->stream();



        return Pdf::loadView('invoice.invoice')
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')

            ->stream();
    }
}
