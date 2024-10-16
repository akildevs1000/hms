<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Customer;
use App\Models\IdCardType;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if ($request->filled('customer_type') && $request->customer_type) {
            $model->where('customer_type', $request->customer_type);
        }

        if ($request->filled('source_id') && $request->source_id) {
            $model->where('source_id', $request->source_id);
        }

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

        return $model->paginate($request->per_page ?? 10000);
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
            ->with("latest_booking")
            ->where('company_id', $request->company_id)
            ->first();

        if ($data) {
            return response()->json(['data' => $data, 'status' => true]);
        } else {
            return response()->json(['data' => [], 'status' => false]);
        }
    }

    public function getCustomerByReservationNo($reservation_no, Request $request)
    {
        $data = Customer::whereHas("latest_booking", fn($q) => $q->where("reservation_no", $reservation_no))
            ->with("latest_booking")
            ->first();

        if ($data) {
            return response()->json(['data' => $data, 'status' => true]);
        } else {
            return response()->json(['data' => [], 'status' => false]);
        }
    }

    public function getCustomerById($id, Request $request)
    {
        $data = Customer::with("latest_booking")->where('id', $id)
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
        $booking = Booking::where('id', $id)->with('bookedRooms', 'payments', 'customer', 'hallBooking.food', 'hallBooking.extraAmounts')
            ->with(["orderRooms" => fn($q) => $q->with("foodplan")])->first();
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
        $customer = Customer::with(['bookings' => ['cityLedgerPayments', 'withOutCityLedgerPayments'], 'idCardType'])
            ->withCount("order_rooms")
            ->find($id);
        $res = $customer->bookings->toArray();
        $bookingIds = array_column($res, 'id');
        $revenue = Payment::whereIn('booking_id', $bookingIds)->where('is_city_ledger', 0)->sum('amount');
        $city_ledger = Payment::whereIn('booking_id', $bookingIds)->where('is_city_ledger', 1)->sum('amount');
        return response()->json(['data' => $customer, 'revenue' => $revenue, 'city_ledger' => $city_ledger, 'status' => true]);
    }

    public function getSourceTransactions($id)
    {
        $customerIds = Customer::where("source_id", $id)->pluck("id");
        $bookings = Booking::with('cityLedgerPayments', 'withOutCityLedgerPayments', "bookedRooms")->whereIn("customer_id", $customerIds)->get()->toArray();
        $bookingIds = array_column($bookings, 'id');
        $total_days = array_sum(array_column($bookings, 'total_days'));
        $revenue = Payment::whereIn('booking_id', $bookingIds)->where('is_city_ledger', 0)->sum('amount');
        $city_ledger = Payment::whereIn('booking_id', $bookingIds)->where('is_city_ledger', 1)->sum('amount');
        return response()->json(['bookings' => $bookings, 'total_days' => $total_days, 'revenue' => $revenue, 'city_ledger' => $city_ledger, 'status' => true]);
    }

    public function getCustomerStatement(Request $request, $id)
    {
        $statement_type = $request->statement_type;

        $customer = Customer::with(['bookings' => ['cityLedgerPayments', 'withOutCityLedgerPayments'], 'idCardType', "company"])
            ->withCount("order_rooms")
            ->find($id);
        $res = $customer->bookings->toArray();
        $bookingIds = array_column($res, 'id');

        $payments = Payment::with("booking")->whereIn('booking_id', $bookingIds)
            // ->where('is_city_ledger', 0)
            ->whereMonth('date', ">=", date("m", strtotime($request->from_date)))
            ->whereMonth('date', "<=", date("m", strtotime($request->to_date)));


        $openBalancePayment = Payment::with("booking")
            ->whereIn('booking_id', $bookingIds)
            ->where('is_city_ledger', 1)
            ->whereMonth('date', "<", date("m", strtotime($request->from_date)))
            ->sum('amount');

        if ($statement_type !== "All") {
            $payments->where('is_city_ledger', 1);
        }

        $paymentData = $payments->get()->toArray();

        $arr = [
            "isOpeningBalance" => true,
            "date" => "---",
            "transaction" => "***Openning Balance***",
            "description" => "---",
            "amount" => 0,
            "payment" => 0,
            "balance" => $openBalancePayment,
        ];


        array_unshift($paymentData, $arr);

        return response()->json([
            'customer' => $customer,
            'company' => $customer->company,
            'statementSum' => $payments->sum('amount'),
            'statementList' => $paymentData,
            'status' => true
        ]);
    }

    public function getSourceStatement(Request $request, $id)
    {
        $statement_type = $request->statement_type;
        $customer_id = $request->customer_id ?? 0;
        $company_id = $request->company_id ?? 0;

        $model = Customer::query();

        $model->where("source_id", $id);

        if ($customer_id) {
            $model->where('id', $customer_id);
        }

        $customerIds =  $model->pluck("id");

        $res = Booking::with('cityLedgerPayments', 'withOutCityLedgerPayments', "bookedRooms")->whereIn("customer_id", $customerIds)->get()->toArray();
        $bookingIds = array_column($res, 'id');

        $payments = Payment::with("booking")->whereIn('booking_id', $bookingIds)
            // ->where('is_city_ledger', 0)
            ->whereDate('date', ">=", $request->from_date)
            ->whereDate('date', "<=", $request->to_date);


        $openBalancePayment = Payment::with("booking")
            ->whereIn('booking_id', $bookingIds)
            ->where('is_city_ledger', 1)
            ->whereDate('date', "<", $request->from_date)
            ->sum('amount');

        if ($statement_type !== "All") {
            $payments->where('is_city_ledger', 1);
        }

        $paymentData = $payments->get()->toArray();

        $arr = [
            "isOpeningBalance" => true,
            "date" => "---",
            "transaction" => "***Openning Balance***",
            "description" => "---",
            "amount" => 0,
            "payment" => 0,
            "balance" => $openBalancePayment,
        ];


        array_unshift($paymentData, $arr);

        return response()->json([
            'company' => Company::find($company_id),
            'statementSum' => $payments->sum('amount'),
            'statementList' => $paymentData,
            'status' => true
        ]);
    }

    public function getCustomerAnalytics(Request $request, $id)
    {
        // Parse the date range
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);

        $payments = Payment::whereIn('booking_id', Booking::whereCustomerId($id)->pluck('id'))
            ->where('is_city_ledger', 0)
            ->whereMonth('date', ">=", date("m", strtotime($request->from_date)))
            ->whereMonth('date', "<=", date("m", strtotime($request->to_date)));


        if (env("APP_ENV") == "local") {
            $payments->select(
                DB::raw('strftime("%m", date) as month'),  // Extract the month (SQLite compatible)
                DB::raw('strftime("%Y", date) as year'),   // Extract the year (SQLite compatible)
                DB::raw('SUM(amount) as total_revenue')
            );
        } else {
            $payments->select(
                DB::raw('to_char(date, \'MM\') as month'),  // Extract the month in PostgreSQL
                DB::raw('to_char(date, \'YYYY\') as year'), // Extract the year in PostgreSQL
                DB::raw('SUM(amount) as total_revenue')
            );
        }

        $data = $payments->groupBy('year', 'month')->get();

        // Prepare an array to hold the revenue for each month in the range
        $statements = [];

        // Loop through each month from the from_date to the to_date
        for ($date = $fromDate; $date <= $toDate; $date->addMonth()) {
            $monthRevenue = $data->firstWhere('month', $date->month);


            $statements[] = [
                "label" => $date->format('M y'),
                "value" => $monthRevenue ? (float) $monthRevenue->total_revenue : 0,
            ];
        }

        return response()->json($statements);
    }


    public function show($id)
    {
        $customer = Customer::find($id);
        return response()->json(['data' => $customer, 'status' => true]);
    }

    public function customer_validate(StoreRequest $request)
    {
        return $this->response('Customer validated.', null, true);
    }

    public function statementPrint($id, $statement_type, $from_date, $to_date)
    {

        $customer = Customer::with(['bookings' => ['cityLedgerPayments', 'withOutCityLedgerPayments'], 'idCardType', "company"])
            ->withCount("order_rooms")
            ->find($id);
        $res = $customer->bookings->toArray();
        $bookingIds = array_column($res, 'id');

        $payments = Payment::with("booking")
            ->whereIn('booking_id', $bookingIds)
            ->whereMonth('date', ">=", date("m", strtotime($from_date)))
            ->whereMonth('date', "<=", date("m", strtotime($to_date)));


        if ($statement_type !== "All") {
            $payments->where('is_city_ledger', 1);
        }


        $first = date('Y-m-d', strtotime($from_date));
        $last = date('Y-m-t', strtotime($to_date));

        return Pdf::loadView('statement.index', [
            'customer' => $customer,
            'company' => $customer->company,
            'statementSum' => $payments->sum('amount'),
            'statementList' => $payments->get(),

            'from' => date('M d, Y', strtotime($first)),
            'to' => date('M d, Y', strtotime($last)),
        ])
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function statementDownload($id, $statement_type, $from_date, $to_date)
    {
        $customer = Customer::with(['bookings' => ['cityLedgerPayments', 'withOutCityLedgerPayments'], 'idCardType', "company"])
            ->withCount("order_rooms")
            ->find($id);
        $res = $customer->bookings->toArray();
        $bookingIds = array_column($res, 'id');

        $payments = Payment::with("booking")
            ->whereIn('booking_id', $bookingIds)
            ->whereMonth('date', ">=", date("m", strtotime($from_date)))
            ->whereMonth('date', "<=", date("m", strtotime($to_date)));


        if ($statement_type !== "All") {
            $payments->where('is_city_ledger', 1);
        }


        $first = date('Y-m-d', strtotime($from_date));
        $last = date('Y-m-t', strtotime($to_date));

        return Pdf::loadView('statement.index', [
            'customer' => $customer,
            'company' => $customer->company,
            'statementSum' => $payments->sum('amount'),
            'statementList' => $payments->get(),

            'from' => date('M d, Y', strtotime($first)),
            'to' => date('M d, Y', strtotime($last)),
        ])
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->download();
    }
}
