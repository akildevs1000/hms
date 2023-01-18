<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store($data, $amount, $paymentType = null)
    {
        $model = Transaction::query();
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

        $trans =  $model->create($data);
        $totalCredit = $model->whereBookingId($trans->booking_id)->sum('credit');
        Booking::find($trans->booking_id)->update(['balance' => $trans->balance, 'paid_amounts' => $totalCredit]);
    }

    public function getTransactionByBookingId(Request $request, $id)
    {
        $transactions = Transaction::whereBookingId($id)->where('company_id', $request->company_id);
        $totalTransactionAmount = $transactions->clone()->orderBy('id', 'desc')->first();

        return response()->json([
            'transactions' => $transactions->get(),
            'status' => true,
            'totalTransactionAmount' => $totalTransactionAmount->balance ?? 0
        ]);
    }
}