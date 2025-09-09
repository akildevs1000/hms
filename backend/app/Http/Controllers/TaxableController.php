<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Taxable;
use Illuminate\Http\Request;

class TaxableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $model = $this->getTaxableProcess($request);

        return $model->paginate($request->per_page ?? 30);
    }
    public function getInvoiceGrandTotal(Request $request)
    {
        //$model = $this->getTaxableProcess($request)->get();

        $model = Booking::where('company_id', $request->company_id)
            ->where('booking_status', '!=', -1)
            ->where('gst_number', '!=', null);

        $model->whereHas('customer', function ($q) {
            $q->where('gst_number', '!=', null);
        });
        if (($request->filled('search') && $request->search)) {

            $model->where(function ($q1) use ($request) {
                $q1->orWhere('gst_number', 'Like', '%' . $request->search . '%');
                $q1->orWhere('reservation_no', 'Like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('from') && ($request->filled('to'))) {

            $model->whereDate('check_in', '>=', $request->from);
            $model->WhereDate('check_in', '<=', $request->to);
        }

        if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {

            $model->WhereDate('check_in', '>=', $request->from);
            $model->whereDate('check_in', '<=', $request->to);
        }

        if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {

            $model->WhereDate('check_out', '>=', $request->from);
            $model->whereDate('check_out', '<=', $request->to);
        }
        $model->with('customer');

        $inv_total_tax_collected         = $model->sum('inv_total_tax_collected');
        $inv_total_without_tax_collected = $model->sum('inv_total_without_tax_collected');

        return ['inv_total_without_tax_collected' => $inv_total_without_tax_collected, 'inv_total_tax_collected' => $inv_total_tax_collected];
    }
    // public function getTaxableProcess($request)
    // {
    //     $model = Booking::where('company_id', $request->company_id)
    //         ->where('booking_status', '!=', -1)
    //         ->where('gst_number', '!=', null);

    //     if (($request->filled('search') && $request->search)) {

    //         $model->where('gst_number', 'Like', '%' . $request->search . '%');
    //         $model->orWhere('reservation_no', 'Like', '%' . $request->search . '%');
    //     }

    //     if ($request->filled('from')   && ($request->filled('to'))) {

    //         $model->whereDate('check_in', '>=', $request->from);
    //         $model->WhereDate('check_in', '<=', $request->to);
    //     }

    //     if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {

    //         $model->WhereDate('check_in', '>=', $request->from);
    //         $model->whereDate('check_in', '<=', $request->to);
    //     }

    //     if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {

    //         $model->WhereDate('check_out', '>=', $request->from);
    //         $model->whereDate('check_out', '<=', $request->to);
    //     }
    //     $model->with('customer');

    //     return  $model->orderBy('check_in', 'ASC');
    // }
    public function getTaxableProcess($request)
    {
        $model = Taxable::where('taxables.company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->whereHas('booking.customer', function ($q) {
                $q->where('gst_number', '!=', null);
            });

        if (($request->filled('search') && $request->search)) {
            $model->whereHas('booking', function ($q) use ($request) {

                // $q->where('gst_number', 'Like', '%' . $request->search . '%');
                // $q->orWhere('reservation_no', 'Like', '%' . $request->search . '%');

                $q->where(function ($q1) use ($request) {
                    $q1->orWhere('gst_number', 'Like', '%' . $request->search . '%');
                    $q1->orWhere('reservation_no', 'Like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('from') && ($request->filled('to'))) {

            $model->whereHas('booking', function ($q) use ($request) {
                $q->whereDate('check_in', '>=', $request->from);
                $q->WhereDate('check_in', '<=', $request->to);
            });
        }

        if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->WhereDate('check_in', '>=', $request->from);
                $q->whereDate('check_in', '<=', $request->to);
            });
        }

        if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->WhereDate('check_out', '>=', $request->from);
                $q->whereDate('check_out', '<=', $request->to);
            });
        }

        $model->with('booking.customer');

        return $model; //->orderBy(Booking::select('check_in')->whereColumn('bookings.id', 'taxables.booking_id'), 'ASC');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function taxableInvoice(Request $request)
    {
        $booking_id = $request->booking_id;
        $company_id = $request->company_id;

        $starting_value = 1000;

        $model = Taxable::query();

        $counter = $model->where('company_id', $company_id)->latest('taxable_invoice_number')->value('taxable_invoice_number') ?? $starting_value;

        $exist = $model->where('company_id', $company_id)->where('booking_id', $booking_id)->exists();

        if (! $exist) {
            $created = $model->create([
                "booking_id"             => $booking_id,
                "taxable_invoice_number" => ++$counter,
                "company_id"             => $company_id,
            ]);

            return $created;
        }

        return "exit";
    }

    public function storeTaxableInvoice($data)
    {
        $booking_id     = $data['id'];
        $reservation_no = $data['reservation_no'];
        $company_id     = $data['company_id'];

        $starting_value = 1000;

        $model = Taxable::query();

        $counter = $model->where('company_id', $company_id)->latest('taxable_invoice_number')->value('taxable_invoice_number') ?? $starting_value;

        $exist = $model->where('company_id', $company_id)->where('booking_id', $booking_id)->exists();

        if (! $exist) {
            $created = $model->create([
                "booking_id"             => $booking_id,
                "taxable_invoice_number" => ++$counter,
                "company_id"             => $company_id,
                "reservation_no"         => $reservation_no,
            ]);

            return $created;
        }

        return "exit";
    }

    public function getInvoices(Request $request)
    {

        $model = Booking::query()->filter(request('search'));

        $model->where(function ($query) {
            $query->whereNotNull('gst_number')
                ->orWhereHas('customer.source', function ($q2) {
                    $q2->whereNotNull('gst');
                });
        });

        $model->where('room_category_type', null);

        // Use a nested group to combine whereHas and orWhereHas
        $model->where(function ($query) use ($request) {
            $query->whereHas('bookedRooms', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        });

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', env("WILD_CARD") ?? 'ILIKE', '%' . $request->source . '%');
        }

        if ($request->isSelectAll != -1) {
            if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
                $model->WhereDate('check_in', '>=', $request->from);
                $model->whereDate('check_in', '<=', $request->to);
            }

            if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
                $model->WhereDate('check_out', '>=', $request->from);
                $model->whereDate('check_out', '<=', $request->to);
            }
            if ($request->guest_mode == '' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
                $model->WhereDate('check_in', '>=', $request->from);
                $model->whereDate('check_in', '<=', $request->to);
            }
        }

        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type,booking_status',
                'customer:id,first_name,last_name,document,source_id,gst_number',
            ])
            ->where('company_id', $request->company_id)
            ->orderBy('created_at', 'asc')
            ->paginate($request->per_page ?? 20);
    }

    public function getInvoice($id, $invoice)
    {
        $booking = Booking::with([
            'orderRooms',
            'customer',
            'company.user',
            'company.contact',
            'transactions',
            'transactions.paymentMode',
            'bookedRooms',
        ])->find($id);

        $lastPaymentModeId = $booking?->transactions?->value("payment_method_id");

        $orderRooms   = $booking->orderRooms;
        $company      = $booking->company;
        $transactions = $booking->transactions;
        $bookedRooms  = $booking->bookedRooms;

        $first_check_in_time  = $bookedRooms[0]["actual_check_in_time"] ?? "00:00";
        $first_check_out_time = $bookedRooms[0]["actual_check_out_time"] ?? "00:00";

        $roomTypes   = array_unique(array_column($booking->bookedRooms->toArray(), 'room_type'));
        $paymentMode = $transactions->toArray();
        $paymentMode = end($paymentMode);

        $amtLatter = amountToText($booking->total_price ?? 0);

        $numberOfCustomers = $booking->bookedRooms->sum(function ($room) {
            return $room->no_of_adult + $room->no_of_child + $room->no_of_baby;
        });

        $roomsDiscount = $booking->bookedRooms->sum(function ($room) {
            return $room->room_discount;
        });

        $bladeName = 'invoice.invoice_updated_with_tax';

        return view($bladeName, compact("invoice", "first_check_in_time", "first_check_out_time", "booking", "orderRooms", "company", "transactions", "amtLatter", "numberOfCustomers", "paymentMode", "roomsDiscount", "roomTypes"));

    }
}
