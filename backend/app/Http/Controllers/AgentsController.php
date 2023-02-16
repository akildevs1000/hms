<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AgentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Agent::query();

        $model->where('company_id', $request->company_id);
        $model->where('type', '!=', 'Customer');
        $model->with('customer', 'booking:id,check_in,check_out,rooms');

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', $request->source);
        }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->whereDate('check_in', '<=', $request->to);
                $q->WhereDate('check_out', '>=', $request->from);
            });
        }
        $model->orderBy('id', 'desc');
        return $model->paginate($request->per_page);
    }

    public function getCityLedger(Request $request)
    {
        $model = Booking::query();
        $model->where('company_id', $request->company_id);
        $model->where('balance', '>', 0);
        $model->where('booking_status', '!=', -1);
        $model->with('customer');
        $model->where(function ($q) {
            $q->where('source', 'walking');
            $q->orWhere('source', 'complimentary');
        });


        // if ($request->filled('search') && $request->search != "") {
        //     $model->whereHas('customer', function ($q) use ($request) {
        //         $q->where('first_name', 'LIKE', "%$request->search%");
        //         $q->orWhere('last_name', 'LIKE', "%$request->search%");
        //     });
        // }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->where(function ($q) use ($request) {
                $q->whereDate('check_in', '<=', $request->to);
                $q->WhereDate('check_out', '>=', $request->from);
            });
        }

        return response()->json([
            'city_ledgers' => $model->paginate($request->per_page ?? 20),
            'message' => '',
            'status' => true

        ]);
    }

    public function getCityLedger1(Request $request)
    {
        $model = Agent::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'Customer');
        $model->with('customer', 'booking:id,check_in,check_out,rooms');

        if ($request->filled('search') && $request->search != "") {

            $model->whereHas('customer', function ($q) use ($request) {
                $q->where('first_name', 'LIKE', "%$request->search%");
                $q->orWhere('last_name', 'LIKE', "%$request->search%");
            });

            if (is_numeric($request->search)) {
                $model->orWhereHas('booking', function ($q) use ($request) {
                    $q->where('id', $request->search);
                });
            }
        }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->whereDate('check_in', '<=', $request->to);
                $q->WhereDate('check_out', '>=', $request->from);
            });
        }

        return $model->paginate($request->per_page ?? 20);
    }

    public function store($data)
    {
        $model = Agent::query();
        return $model->create($data);
    }

    public function update($data, $foundAgent)
    {
        return $foundAgent->update(['amount' => $data['amount']]);
    }

    public function getAgentBookings(Request $request)
    {
        $model = Booking::where('company_id', $request->company_id)
            ->find($request->id);
        $totalCredit = Transaction::whereBookingId($request->id)->where('company_id', $request->company_id)->sum('credit');
        return response()->json(['status' => true, 'data' => $model, 'totalCredit' => $totalCredit]);
    }

    public function getAgentDetails(Request $request)
    {
        $model = Agent::where('company_id', $request->company_id)
            ->where('source', $request->source)
            ->with('booking', 'customer')
            ->get();

        return response()->json(['status' => true, 'data' => $model]);
    }

    private function paidOnlyRooms($booking, $request)
    {
        if ($booking->total_price <= $request->full_payment) {
            $booking->grand_remaining_price = (int) $booking->grand_remaining_price - (int) $request->full_payment;
            $booking->remaining_price       = 0;
            $booking->payment_status       = 0;
        } else {
            $booking->remaining_price = ((int) $booking->total_price - (int) $request->full_payment);
            $booking->grand_remaining_price =  ((int) $booking->remaining_price + (int) $booking->total_posting_amount);
            $booking->payment_status = 0;
        }
        return $booking->save();
    }
    private function paidOnlyPosting($booking, $request)
    {
        if ($booking->total_posting_amount <= $request->full_payment) {
            $booking->grand_remaining_price = (int) $booking->grand_remaining_price - (int) $booking->total_posting_amount;
            $booking->total_posting_amount  = 0;
            $booking->payment_status       = 0;
        } else {
            $booking->total_posting_amount = ((int) $booking->total_posting_amount - (int) $request->full_payment);
            $booking->grand_remaining_price =  ((int) $booking->remaining_price + (int) $booking->total_posting_amount);
            $booking->payment_status = 0;
        }
        return $booking->save();
    }
    private function paidWithAll($booking, $request)
    {
        if ($booking->grand_remaining_price <= $request->full_payment) {
            $booking->full_payment          = $booking->grand_remaining_price;
            $booking->remaining_price       = 0;
            $booking->grand_remaining_price = 0;
            $booking->total_posting_amount  = 0;
            $booking->payment_status = 1;
        } else {
            $booking->remaining_price = ((int) $booking->total_price - (int) $request->full_payment);
            $booking->grand_remaining_price =  ((int) $booking->remaining_price + (int) $booking->total_posting_amount);
            $booking->payment_status = 0;
        }
        return $booking->save();
    }

    public function paymentByAgent(Request $request)
    {
        $agentId       = $request->agentData['id'];
        $booking_id    = $request->booking_id;
        $paid_status   = (int) $request->paid_status;
        $booking       = Booking::find($booking_id);
        $isFullPayment = false;
        switch ($paid_status) {
            case 1:
                $onlyRoom =  $this->paidOnlyRooms($booking, $request);
                break;
            case 2:
                $onlyPosting = $this->paidOnlyPosting($booking, $request);
                break;
            case 3:
                $fullPayment = $this->paidWithAll($booking, $request);
                break;
        }

        $transactionData = [
            'booking_id'        => $booking->id,
            'customer_id'       => $booking->customer_id ?? '',
            'date'              => now(),
            'company_id'        => $booking->company_id ?? '',
            'payment_method_id' => $booking->payment_mode_id,
        ];
        $transaction = new TransactionController();
        $transaction->store($transactionData, $request->full_payment, 'credit');

        $paymentsData = [
            'booking_id'     => $booking_id,
            'payment_mode'   => $request->payment_mode_id,
            'description'    => 'full payment by ' . $request->agentData['source'] ?? '',
            'amount'         => $request->full_payment,
            'type'           => 'agent',
            'room'           => $booking->rooms,
            'company_id'     => $booking->company_id,
            'is_city_ledger' => 0,
            'created_at'     => now(),
        ];

        $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
        if ($payment) {
            $payment->amount = (int) $payment->amount - (int) $request->full_payment;
            $payment->save();
        }

        $payment = new PaymentController();
        $payment->store($paymentsData);

        $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
        Agent::find($agentId)->update([
            'is_paid'      => true,
            'paid_date'    => date('Y-m-d'),
            'payment_mode' => $request->payment_mode_id,
            'transaction'  => $request->transaction,
            'agent_paid_amount'  => $totCredit,
            'agent_paid_amount'  => $request->full_payment,
            'is_paid_with_posting'  => $paid_status == 3 ? 1 : 0,
            'is_full_payment'  => $booking->grand_remaining_price <= $request->full_payment ? 1 : 0,
        ]);

        return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);

        // =============================

        $agentId       = $request->agentData['id'];
        $booking_id    = $request->booking_id;
        $booking       = Booking::find($booking_id);
        $isFullPayment = false;
        if ($booking->remaining_price <= $request->full_payment) {
            $booking->remaining_price = 0;
            $booking->payment_status  = 1;
            $booking->full_payment    = $booking->total_price;
            $isFullPayment            = true;
        } else {
            $booking->remaining_price = ((int) $booking->total_price - (int) $request->full_payment);
        }
        $booking->payment_mode_id = $request->payment_mode_id;
        $booking->check_out_price = $request->full_payment;
        // $booking->booking_status  = 3;
        if ($booking->save()) {

            if ($isFullPayment) {
                $transactionData = [
                    'booking_id'        => $booking->id,
                    'customer_id'       => $booking->customer_id ?? '',
                    'date'              => now(),
                    'company_id'        => $booking->company_id ?? '',
                    'payment_method_id' => $booking->payment_mode_id,
                ];

                $payment = new TransactionController();
                $payment->store($transactionData, $request->full_payment, 'credit');
            }

            //  =====================

            $paymentsData = [
                'booking_id'     => $booking_id,
                'payment_mode'   => $request->payment_mode_id,
                'description'    => 'full payment by ' . $request->agentData['source'] ?? '',
                'amount'         => $request->full_payment,
                'type'           => 'agent',
                'room'           => $booking->rooms,
                'company_id'     => $booking->company_id,
                'is_city_ledger' => 0,
                'created_at'     => now(),
            ];

            // $found = Payment::where('booking_id', $booking_id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            // if ($found) {
            //     $found->update($paymentsData);
            // }



            Agent::find($agentId)->update(['is_paid' => true, 'paid_date' => date('Y-m-d'), 'payment_mode' => $request->payment_mode_id, 'transaction' => $request->transaction]);
            return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);
        }

        return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    }

    public function paymentByAgentOld(Request $request)
    {
        $agentId       = $request->agentData['id'];
        $booking_id    = $request->booking_id;
        $paid_status   = (int) $request->paid_status;
        $booking       = Booking::find($booking_id);
        $isFullPayment = false;
        switch ($paid_status) {
            case 1:
                $onlyRoom =  $this->paidOnlyRooms($booking, $request);
                break;
            case 2:
                $onlyPosting = $this->paidOnlyPosting($booking, $request);
                break;
            case 3:
                $fullPayment = $this->paidWithAll($booking, $request);
                break;
        }

        $transactionData = [
            'booking_id'        => $booking->id,
            'customer_id'       => $booking->customer_id ?? '',
            'date'              => now(),
            'company_id'        => $booking->company_id ?? '',
            'payment_method_id' => $booking->payment_mode_id,
        ];
        $transaction = new TransactionController();
        $transaction->store($transactionData, $request->full_payment, 'credit');

        $paymentsData = [
            'booking_id'     => $booking_id,
            'payment_mode'   => $request->payment_mode_id,
            'description'    => 'full payment by ' . $request->agentData['source'] ?? '',
            'amount'         => $request->full_payment,
            'type'           => 'agent',
            'room'           => $booking->rooms,
            'company_id'     => $booking->company_id,
            'is_city_ledger' => 0,
            'created_at'     => now(),
        ];

        $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
        if ($payment) {
            $payment->amount = (int) $payment->amount - (int) $request->full_payment;
            $payment->save();
        }

        $payment = new PaymentController();
        $payment->store($paymentsData);

        $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
        Agent::find($agentId)->update([
            'is_paid'      => true,
            'paid_date'    => date('Y-m-d'),
            'payment_mode' => $request->payment_mode_id,
            'transaction'  => $request->transaction,
            'agent_paid_amount'  => $totCredit,
            'agent_paid_amount'  => $request->full_payment,
            'is_paid_with_posting'  => $paid_status == 3 ? 1 : 0,
            'is_full_payment'  => $booking->grand_remaining_price <= $request->full_payment ? 1 : 0,
        ]);

        return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);

        // =============================

        $agentId       = $request->agentData['id'];
        $booking_id    = $request->booking_id;
        $booking       = Booking::find($booking_id);
        $isFullPayment = false;
        if ($booking->remaining_price <= $request->full_payment) {
            $booking->remaining_price = 0;
            $booking->payment_status  = 1;
            $booking->full_payment    = $booking->total_price;
            $isFullPayment            = true;
        } else {
            $booking->remaining_price = ((int) $booking->total_price - (int) $request->full_payment);
        }
        $booking->payment_mode_id = $request->payment_mode_id;
        $booking->check_out_price = $request->full_payment;
        // $booking->booking_status  = 3;
        if ($booking->save()) {

            if ($isFullPayment) {
                $transactionData = [
                    'booking_id'        => $booking->id,
                    'customer_id'       => $booking->customer_id ?? '',
                    'date'              => now(),
                    'company_id'        => $booking->company_id ?? '',
                    'payment_method_id' => $booking->payment_mode_id,
                ];

                $payment = new TransactionController();
                $payment->store($transactionData, $request->full_payment, 'credit');
            }

            //  =====================

            $paymentsData = [
                'booking_id'     => $booking_id,
                'payment_mode'   => $request->payment_mode_id,
                'description'    => 'full payment by ' . $request->agentData['source'] ?? '',
                'amount'         => $request->full_payment,
                'type'           => 'agent',
                'room'           => $booking->rooms,
                'company_id'     => $booking->company_id,
                'is_city_ledger' => 0,
                'created_at'     => now(),
            ];

            // $found = Payment::where('booking_id', $booking_id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            // if ($found) {
            //     $found->update($paymentsData);
            // }



            Agent::find($agentId)->update(['is_paid' => true, 'paid_date' => date('Y-m-d'), 'payment_mode' => $request->payment_mode_id, 'transaction' => $request->transaction]);
            return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);
        }

        return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    }

    public function paymentByCustomer(Request $request)
    {
        $booking_id = $request->booking_id;
        $booking    = Booking::where('company_id', $request->company_id)->find($booking_id);

        $transactionData = [
            'booking_id'        => $booking->id,
            'customer_id'       => $booking->customer_id ?? '',
            'date'              => now(),
            'company_id'        => $booking->company_id ?? '',
            'payment_method_id' => $booking->payment_mode_id,
            'desc' => 'paid by city ledger',
        ];

        $payment = new TransactionController();
        $payment->store($transactionData, $request->full_payment, 'credit');
        $booking = Booking::find($booking_id);
        if ($booking) {
            if ($booking->balance > 0) {
                $booking->payment_status  = 0;
                $booking->remaining_price       =  (int)$booking->remaining_price - (int)$request->full_payment;
                $booking->grand_remaining_price = (int)$booking->remaining_price + (int)$booking->total_posting_amount;;

                $paymentsData = [
                    'booking_id'     => $booking_id,
                    'payment_mode'   => $request->payment_mode_id,
                    'description'    =>  'payment by ' . $request->cityLedgerData['source'] ?? '',
                    'amount'         => $request->full_payment,
                    'type'           => 'customer',
                    'room'           => $booking->rooms,
                    'company_id'     => $booking->company_id,
                    'is_city_ledger' => 0,
                    'created_at'     => now(),
                ];
                $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                if ($payment) {
                    $payment->amount = (int) $booking->balance;
                    $payment->save();
                }
                $payment = new PaymentController();
                $payment->store($paymentsData);
            } else {
                $booking->payment_status  = 1;
                $booking->full_payment    = $booking->paid_amounts;
                $booking->remaining_price       = 0;
                $booking->grand_remaining_price = 0;
                $booking->total_posting_amount  = 0;
            }
            $booking->save();
            return response()->json(['data' => $booking_id, 'message' => 'Successfully Paid', 'status' => true]);
        }
        return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    }
    public function paymentByCustomer1(Request $request)
    {
        $agentId    = $request->agentData['id'];
        $booking_id = $request->booking_id;
        $booking    = Booking::find($booking_id);






        $isFullPayment = false;
        if ($booking->grand_remaining_price <= $request->full_payment) {
            $booking->payment_status  = 1;
            $booking->full_payment    = $booking->total_price;
            $booking->remaining_price       = 0;
            $booking->grand_remaining_price = 0;
            $booking->total_posting_amount  = 0;
            $isFullPayment = true;
        } else {
            $booking->grand_remaining_price = ((int) $booking->grand_remaining_price - (int) $request->full_payment);
        }
        $booking->payment_mode_id = $request->payment_mode_id;
        if ($booking->save()) {

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $booking->payment_mode_id,
                'desc' => 'payment by city ledger',
            ];

            $payment = new TransactionController();
            $payment->store($transactionData, $request->full_payment, 'credit');

            $paymentsData = [
                'booking_id'     => $booking_id,
                'payment_mode'   => $request->payment_mode_id,
                'description'    =>  'payment by ' . $request->agentData['source'] ?? '',
                'amount'         => $request->full_payment,
                'type'           => 'customer',
                'room'           => $booking->rooms,
                'company_id'     => $booking->company_id,
                'is_city_ledger' => 0,
                'created_at'     => now(),
            ];

            $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            if ($payment) {
                $payment->amount = (int) $payment->amount - (int) $request->full_payment;
                $payment->save();
            }
            $payment = new PaymentController();
            $payment->store($paymentsData);

            $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
            Agent::find($agentId)->update([
                'is_paid'      => $isFullPayment ? 1 : 0,
                'paid_date'    => $isFullPayment ? date('Y-m-d') : '',
                'payment_mode' => $request->payment_mode_id,
                'transaction'  => $request->transaction,
                'agent_paid_amount'  => $totCredit,
                'agent_paid_amount'  => $request->full_payment,
                'is_paid_with_posting'  => 1,
                'is_full_payment'  => $booking->grand_remaining_price <= $request->full_payment ? 1 : 0,
            ]);



            return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);
        }

        return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    }
}
