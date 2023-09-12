<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
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

        $trans = $model->create($data);
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
            'totalTransactionAmount' => $totalTransactionAmount->balance ?? 0,
        ]);
    }

    // public function removeSingleTransaction(Request $request)
    // {
    //     $deleted = true;
    //     $model   = Transaction::query();
    //     $deleted = $model->clone()->find($request->id)->delete();

    //     if ($deleted) {
    //         $totalCredit            = $model->clone()->whereBookingId($request->booking_id)->sum('credit');
    //         return $lastTransaction = $model->clone()->whereBookingId($request->booking_id)->first();

    //         return;
    //         Booking::find($trans->booking_id)->update(['balance' => $trans->balance, 'paid_amounts' => $totalCredit]);
    //         return $request->all();
    //     }

    // }

    public function getTransactionSummaryByBookingId($id)
    {
        $transactionModel = Transaction::whereBookingId($id);

        return [
            'sumDebit' => $transactionModel->sum('debit'),
            'sumCredit' => $transactionModel->sum('credit'),
            'balance' => (float) $transactionModel->sum('debit') - (float) $transactionModel->sum('credit'),
            'tot_posting' => $transactionModel->whereIsPosting(1)->sum('debit'),
        ];
    }

    public function updateBookingByTransactions($bookingId, $amt = 0)
    {
        $transactionModel = Transaction::whereBookingId($bookingId);
        $sumDebit = $transactionModel->sum('debit');
        $sumCredit = $transactionModel->sum('credit');
        $balance = $sumDebit - $sumCredit;

        $model = booking::find($bookingId);

        $model->update([
            // 'total_price' => $model->total_price + $amt,
            'total_price' => $sumDebit,
            'grand_remaining_price' => $balance,
            'balance' => $balance,
            'remaining_price' => $balance,
            'paid_amounts' => $sumCredit,
        ]);
    }

    public function getTransactionByUsers(Request $request)
    {
        // return Transaction::where('company_id', $request->company_id)->get()->groupBy('user_id');

        $model = User::query();
        $model->where('company_id', $request->company_id);

        $model->when($request->user_id && $request->filled('user_id'), function ($q) use ($request) {
            $q->where('id', $request->user_id);
        });

        $model->withSum('transactions', 'credit');

        $model->with(['transactions' => function ($query) use ($request) {
            $query->when($request->filled('from_date') && $request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
                $q->whereDate('created_at', '<=', $request->to_date);
            });
            $query->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            });
            $query->with('paymentMode', 'booking:id,reservation_no,rooms');
        }]);



        return $model->get();
    }
}
