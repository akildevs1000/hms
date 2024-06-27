<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\IdCardType;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function dropDown()
    {
        return Customer::get();
    }
    public function index(Request $request, Customer $model)
    {
        $model = Customer::query();

        $model->where('company_id', $request->company_id);
        $model->with('idCardType');

        if ($request->filled('sortBy')) {
            $sortDesc = $request->sortDesc == 'true' ? 'DESC' : 'ASC';
            $sortBy = $request->sortBy;

            if (strpos($sortBy, '.') === -1 || strpos($sortBy, '.') == '') {
                $model->orderBy($sortBy, $sortDesc);
            } else {
                if ($sortBy == 'id_card_type.name') {
                    $model->orderBy(IdCardType::select('name')
                        ->whereRaw('id_card_types.id = CAST(customers.id_card_type_id AS bigint)'), $sortDesc);
                }
            }
        } else {
            $model->orderBy('updated_at', 'DESC');
        }

        return $model->paginate($request->per_page);
    }

    public function store(StoreRequest $request)
    {
        try {

            $customer = Customer::whereContactNo($request->contact_no)->first();
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

    public function storeNewCustomer(StoreRequest $request)
    {
        try {
            $customer = Customer::create($request->validated());
            if ($customer) {
                if ($request->hasFile('document')) {
                    $file = $request->file('document');
                    $ext = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $ext;
                    $path = $file->storeAs('public/documents/booking', $fileName);
                    Storage::copy($path, 'public/documents/customer/' . $fileName);
                    $customer->document = $fileName;
                }
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $ext;
                    $path = $file->storeAs('public/documents/customer/photo', $fileName);
                    $customer->image = $fileName;
                }
                $customer->save();

                return $this->response('Customer successfully added.', $customer->id, true);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            // return $request->hasFile('image');
            $customer = Customer::find($request->id);
            $updated = Customer::find($request->id)->update($request->validated());
            if ($updated) {
                if ($request->hasFile('document')) {
                    $file = $request->file('document');
                    $ext = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $ext;
                    $path = $file->storeAs('public/documents/booking', $fileName);
                    Storage::copy($path, 'public/documents/customer/' . $fileName);
                    $customer->document = $fileName;
                }
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $ext;
                    $path = $file->storeAs('public/documents/customer/photo', $fileName);
                    $customer->image = $fileName;
                }
                $customer->save();
                return $this->response('Customer successfully updated.', null, true);
            }
            return $this->response('something wrong.', null, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function search(Request $request, $key)
    {
        // return $key;
        return Customer::query()
            ->latest()
            ->filter($key)
            ->with('idCardType')
            ->where('company_id', $request->company_id)
            ->paginate($request->perPage ?? 20);
    }

    public function getCustomer($id, Request $request)
    {
        $data = Customer::where('contact_no', $id)
            ->where('company_id', $request->company_id)
            ->first();

        if ($data) {
            return response()->json(['data' => $data, 'status' => true]);
        } else {
            return response()->json(['data' => [], 'status' => false]);
        }
    }

    public function getCustomerById($id, Request $request)
    {
        $data = Customer::where('id', $id)
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
        $booking = Booking::where('id', $id)->with('bookedRooms', 'payments', 'customer', 'orderRooms', 'hallBooking.food', 'hallBooking.extraAmounts')->first();
        $postings = Posting::with('room')->whereBookingId($id)->get();
        // $totalPostingAmount = Posting::whereBookingId($id)->sum('amount_with_tax');
        $transaction = Transaction::with(['paymentMode', 'user'])->whereBookingId($id);
        $transactions = $transaction->clone()->orderBy('id', 'asc')->get();
        $totalTransactionAmount = $transaction->clone()->orderBy('id', 'desc')->first();

        $transactionSummary = (new TransactionController)->getTransactionSummaryByBookingId($id);

        return response()->json([
            'booking' => $booking,
            // 'totalPostingAmount' => $totalPostingAmount,
            'transaction' => $transactions,
            'totalTransactionAmount' => $totalTransactionAmount->balance ?? 0,
            'transactionSummary' => $transactionSummary,
            'postings' => $postings,
        ]);
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

    public function show($id)
    {
        $customer = Customer::find($id);
        return response()->json(['data' => $customer, 'status' => true]);
    }
}
