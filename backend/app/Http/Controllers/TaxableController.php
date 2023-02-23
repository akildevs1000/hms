<?php

namespace App\Http\Controllers;

use App\Models\Taxable;
use Illuminate\Http\Request;

class TaxableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        return Taxable::whereCompanyId($request->company_id)->with('booking.customer')->paginate($request->per_page ?? 30);
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
                "booking_id" => $booking_id,
                "taxable_invoice_number" => ++$counter,
                "company_id" => $company_id,
            ]);

            return $created;
        }

        return "exit";
    }

    public function storeTaxableInvoice($data)
    {
        $booking_id = $data['id'];
        $reservation_no = $data['reservation_no'];
        $company_id = $data['company_id'];

        $starting_value = 1000;

        $model = Taxable::query();

        $counter = $model->where('company_id', $company_id)->latest('taxable_invoice_number')->value('taxable_invoice_number') ?? $starting_value;

        $exist = $model->where('company_id', $company_id)->where('booking_id', $booking_id)->exists();

        if (!$exist) {
            $created = $model->create([
                "booking_id" => $booking_id,
                "taxable_invoice_number" => ++$counter,
                "company_id" => $company_id,
                "reservation_no" => $reservation_no,
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
