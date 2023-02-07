<?php

namespace App\Http\Controllers;

use App\Models\Booking;

use App\Models\Payment;
use App\Models\Posting;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;

class CustomerController extends Controller
{
    public function index(Request $request, Customer $model)
    {
        return $model
            ->where('company_id', $request->company_id)
            ->with('idCardType')
            ->paginate($request->per_page);
    }

    public function store(StoreRequest $request)
    {
        try {

            $customer =   Customer::whereContactNo($request->contact_no)->first();
            $id = "";

            if ($customer) {
                $id = $customer->id;
                $customer->update($request->validated());
            } else {
                $record = Customer::create($request->validated());
                $id = $record->id;
            }
            return $this->response('Customer successfully added.', $id, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function update(UpdateRequest $request)
    {
        try {
            $updated = Customer::find($request->id)->update($request->validated());
            if ($updated) {
                return $this->response('Customer successfully updated.', null, true);
            }
            return $this->response('something wrong.', null, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    // public function search(Request $request, $key)
    // {

    //     $model  = Customer::query();

    //     $fields = [
    //         'name',
    //         'contact_no',
    //         'email',
    //         "id_card_type_id",
    //         "id_card_no",
    //         'car_no',
    //         "no_of_adult",
    //         "no_of_child",
    //         "no_of_baby",
    //         "address",
    //     ];

    //     $model = $this->process_search($model, $key, $fields);


    //     return  $model->where("company_id", $request->company_id)->get();

    //     return $model->paginate($request->perPage);
    // }


    public function search(Request $request, $key)
    {
        return Customer::query()
            ->latest()
            ->filter($key)
            ->where('company_id', $request->company_id)
            ->paginate($request->perPage ?? 20);
    }





    public function getCustomer($id, Request $request)
    {
        $data =  Customer::where('contact_no', $id)
            ->where('company_id', $request->company_id)
            ->first();

        if ($data) {
            return response()->json(['data' => $data, 'status' => true]);
        } else {
            return response()->json(['data' => [], 'status' => false]);
        }
    }


    public function bookingCustomers(Request $request)
    {
        return Customer::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0)
                ->where('booking_status', '<=', 3);
        })
            ->with('booking.payments')
            ->paginate($request->per_page ?? 20);
    }

    public function viewBookingCustomerBill($id)
    {
        $booking =  Booking::where('id', $id)->with('bookedRooms', 'payments', 'customer')->first();

        $totalPostingAmount = Posting::whereBookingId($id)->sum('amount_with_tax');

        $transaction = Transaction::whereBookingId($id);
        $transactions = $transaction->clone()->get();
        $totalTransactionAmount = $transaction->clone()->orderBy('id', 'desc')->first();

        return response()->json([
            'booking' => $booking,
            'totalPostingAmount' => $totalPostingAmount,
            'transaction' => $transactions,
            'totalTransactionAmount' => $totalTransactionAmount->balance ?? 0
        ]);


        // return Customer::whereHas('booking', function ($q) {
        //     $q->where('booking_status', '!=', 0)
        //         ->where('booking_status', '<=', 3);
        // })->with(
        //     ['booking' => [
        //         'bookedRooms',
        //         'payments',
        //     ]]
        // )
        //     ->find($id);

    }

    public function getCustomerHistory($id)
    {
        $customer = Customer::with(['bookings' => ['cityLedgerPayments', 'withOutCityLedgerPayments'], 'idCardType'])->find($id);
        $res = $customer->bookings->toArray();
        $bookingIds = array_column($res, 'id');
        $revenue = Payment::whereIn('booking_id', $bookingIds)->where('is_city_ledger', 0)->sum('amount');
        $city_ledger = Payment::whereIn('booking_id', $bookingIds)->where('is_city_ledger', 1)->sum('amount');
        return response()->json(['data' => $customer, 'revenue' => $revenue, 'city_ledger' => $city_ledger, 'status' => true]);
    }
}