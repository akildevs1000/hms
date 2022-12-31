<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store($data, $amount, $paymentType = null)
    {
        $model = Transaction::query();

        // $data = [
        //     'booking_id' => $booked->id,
        //     'customer_id' => $booked->customer_id ?? '',
        //     'date' => now(),
        //     'company_id' => $request->company_id ?? '',
        //     'payment_method_id' => $booked->payment_mode_id,
        // ];

        $payment = $model->whereBookingId($data['booking_id'])->orderBy('id', 'desc')->first();

        if ($payment) {
            switch ($paymentType) {
                case 'credit':
                    $data['credit'] = $amount;
                    $data['balance'] = $payment->balance - $amount;
                    break;
                case 'debit':
                    $data['debit'] = $amount;
                    $data['balance'] = $payment->balance + $amount;
            }
        } else {
            $data['debit'] = $amount;
            $data['balance'] = $amount;
        }

        return  $model->create($data);
    }

    public function getTransactionByBookingId(Request $request, $id)
    {
        $transactions = Transaction::whereBookingId($id)->where('company_id', $request->company_id);
        $totalTransactionAmount = $transactions->clone()->latest()->first();

        return response()->json([
            'transactions' => $transactions->get(),
            'status' => true,
            'totalTransactionAmount' => $totalTransactionAmount->balance ?? 0
        ]);
    }
}