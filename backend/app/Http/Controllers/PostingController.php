<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Posting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostingController extends Controller
{

    public function index(Request $request)
    {
        $model = Posting::query();
        $model->where('company_id', $request->company_id);

        $model->with([
            'booking:id,reservation_no,customer_id,booking_status' => [
                'customer:id,email,first_name,last_name',
            ],
            'bookedRoom',
        ]);

        // filter base booking id (Reservation Number)
        // $model->when($request->filled('booking_id'), function ($model) use ($request) {
        //     $model->whereHas('booking', function (Builder $query) use ($request) {
        //         $query->where('id', $request->booking_id);
        //     });
        // });

        // filter base room id
        $model->when($request->filled('room_id'), function ($model) use ($request) {
            $model->whereHas('bookedRoom', function (Builder $query) use ($request) {
                $query->where('room_id', $request->room_id);
            });
        });

        //filter base customer id
        $model->when($request->filled('customer_id'), function ($model) use ($request) {
            $model->whereHas('booking', function (Builder $query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            });
        });

        return $model->paginate($request->per_page ?? 50);
    }

    public function search(Request $request, $key)
    {
        $model = Posting::query();
        $model->where('company_id', $request->company_id);

        // $fields = [
        //     'bill_no',
        // ];
        // $model = $this->process_search($model, $key, $fields);

        $model->with([
            'booking:id,customer_id,room_id' => [
                'customer:id,email,first_name,last_name',
            ],
            'bookedRoom:id,room_no,room_type',
        ]);

        $model->Where('bill_no', $key);
        $model->when($key && isset($key), function ($model) use ($key) {
            $model->orWhereHas('booking', function (Builder $query) use ($key) {
                $query->where('id', is_numeric($key) ? $key : 0)
                    ->orWhereHas('customer', function (Builder $query) use ($key) {
                        $query->where('first_name', 'Like', '%' . $key . '%')
                            ->orWhere('last_name', 'Like', '%' . $key . '%');
                    });
            });
        });

        return $model->paginate($request->per_page ?? 50);
    }

    public function store(Request $request)
    {
        try {
            $data                 = $request->except('room');
            $room_no              = BookedRoom::find($data['booked_room_id'])->room_no;
            $booking              = Booking::find($data['booking_id']);
            $data['posting_date'] = now();
            $posting              = Posting::create($data);

            $transactionData = [
                'booking_id'  => $data['booking_id'],
                'customer_id' => 1 ?? '',
                'date'        => now(),
                'desc'        => "posting reservation no " . $booking['reservation_no'] . " and room no " . $room_no ?? "",
                'company_id'  => $data['company_id'] ?? '',
                'is_posting'  => 1,
            ];

            $transaction = new TransactionController();
            $transaction->store($transactionData, $posting->amount_with_tax, 'debit');

            $booking                        = Booking::where('company_id', $request->company_id)->find($request->booking_id);
            $booking->total_posting_amount  = (int) $booking->total_posting_amount + $posting->amount_with_tax;
            $booking->grand_remaining_price = (int) $posting->amount_with_tax + $booking->grand_remaining_price;
            $booking->save();

            $agent = Agent::whereBookingId($request->booking_id)->where('company_id', $request->company_id)->first();
            if ($agent) {
                $agent->posting_amount = (int) $agent->posting_amount + $posting->amount_with_tax;
                $agent->save();
            }

            $payment         = Payment::where('booking_id', $request->booking_id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            $payment->amount = (int) $payment->amount + $posting->amount_with_tax;
            $payment->save();

            return $this->response('Posting Successfully submitted.', $posting, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(Request $request, $id)
    {
        $model = Posting::query();
        $model->where('company_id', $request->company_id);

        $model->with([
            'bookedRoom',
            'booking:id,customer_id,rooms' => [
                'customer:id,email,first_name,last_name',
                'room:id,room_type_id,room_no',
            ],
        ]);

        return $model->whereBookedRoomId($id)->get();
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function cancel(Posting $posting)
    {
        try {
            $room_no = BookedRoom::find($posting->booked_room_id)->room_no;
            $transactionData = [
                'booking_id'  => $posting->booking_id,
                'customer_id' =>  '1',
                'date'        => now(),
                'desc'        => "cancel posting reservation no " . $posting->reservation_no . " and room no " . $room_no ?? "",
                'company_id'  => $posting->company_id ?? '',
                'is_posting'  => 1,
            ];
            (new TransactionController)->store($transactionData, -$posting->amount_with_tax, 'debit');
            (new TransactionController)->updateBookingByTransactions($posting->booking_id, 0);
            $agent = Agent::whereBookingId($posting->booking_id)->where('company_id', $posting->company_id)->first();
            if ($agent) {
                $agent->posting_amount = (int) $agent->posting_amount - $posting->amount_with_tax;
                $agent->save();
            }
            $payment         = Payment::where('booking_id', $posting->booking_id)->where('company_id', $posting->company_id)->where('is_city_ledger', 1)->first();
            $payment->amount = (int) $payment->amount - $posting->amount_with_tax;
            $payment->save();
            $posting->delete();

            return $this->response('Posting Successfully canceled.', '', true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
