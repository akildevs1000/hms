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

        if ($request->filled('from')   && ($request->filled('to'))) {


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

        $inv_total_tax_collected = $model->sum('inv_total_tax_collected');
        $inv_total_without_tax_collected = $model->sum('inv_total_without_tax_collected');

        return ['inv_total_without_tax_collected' => $inv_total_without_tax_collected, 'inv_total_tax_collected' => $inv_total_tax_collected,];
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
        $model = Taxable::query();

        $model->where('taxables.company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            });
        $model->whereHas('booking.customer', function ($q) {
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

        if ($request->filled('from')   && ($request->filled('to'))) {

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

        return  $model->orderBy(Booking::select('check_in')->whereColumn('bookings.id', 'taxables.booking_id'), 'ASC');
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

        if (!$exist) {
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

        if (!$exist) {
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Taxable  $taxable
     * @return \Illuminate\Http\Response
     */
    public function show(Taxable $taxable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Taxable  $taxable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Taxable $taxable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Taxable  $taxable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taxable $taxable)
    {
        //
    }
}
