<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use NumberFormatter;
use App\Models\Booking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{

    public function index($id)
    {

        $booking = Booking::with(['orderRooms', 'customer', 'company' => ['user', 'contact'], 'transactions.paymentMode', 'bookedRooms'])
            ->find($id);

        $orderRooms = $booking->orderRooms;
        $company = $booking->company;
        $transactions = $booking->transactions;
        $bookedRooms = $booking->bookedRooms;


        $roomTypes =    array_column($booking->bookedRooms->toArray(), 'room_type');
        $paymentMode = $transactions->toArray();
        $paymentMode = end($paymentMode);

        $amtLatter =  $this->amountToText($transactions->sum('debit') ?? 0);
        $numberOfCustomers = $booking->bookedRooms->sum(function ($room) {
            return $room->no_of_adult + $room->no_of_child + $room->no_of_baby;
        });

        $roomsDiscount = $booking->bookedRooms->sum(function ($room) {
            return $room->room_discount;
        });

        return view('invoice.invoice', compact("booking", "orderRooms", "company", "transactions", "amtLatter", "numberOfCustomers", "paymentMode", "roomsDiscount", "roomTypes"));

        return Pdf::loadView('invoice.invoice', compact("booking", "orderRooms", "company", "transactions", "amtLatter", "numberOfCustomers", "paymentMode", "roomsDiscount"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }
    public function printInvoice($id)
    {
        // return $booking = Booking::with('orderRooms.postings', 'customer')->find($id);
        $booking = Booking::with('orderRooms', 'customer')->find($id);
        $orderRooms = $booking->orderRooms;
        return Pdf::loadView('invoice.invoice', compact("booking", "orderRooms"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function amountToText($amount)
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
        $text = ucwords($formatter->format($amount));
        return $text;
    }


    public function grc($id)
    {
        $booking = Booking::with(['orderRooms', 'customer', 'company' => ['user', 'contact'], 'transactions', 'bookedRooms'])->find($id);
        $trans = (new TransactionController)->getTransactionSummaryByBookingId($id);
        return Pdf::loadView('grc.index', compact('booking', 'trans'))
            ->setPaper('a4', 'portrait')
            ->stream();
    }
}
