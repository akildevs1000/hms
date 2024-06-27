<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\ValidationRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return Invoice::get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Invoice::with(["customer", "items","quotation"])->paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        // Invoice::truncate();
        // InvoiceItem::truncate();

        try {
            DB::beginTransaction();
            $data = $request->validated();

            $data["bank_details"] = "null";
            $data["terms_and_conditions"] = "null";

            $lastInvoiceId = Invoice::max("id") + 1;

            $ref_no = $lastInvoiceId < 1000 ? $lastInvoiceId + 1000 : $lastInvoiceId;

            $data["ref_no"] = "INV-$ref_no";

            $data["quotation_id"] = $request->id ?? 0;

            $invoice =  Invoice::create($data);

            $items = array_map(function ($item) use ($invoice) {
                $item['invoice_id'] = $invoice->id;
                unset($item['quotation_id']);
                $item['created_at'] = now();
                return $item;
            }, $request->items);

            InvoiceItem::insert($items);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception or handle it as necessary
            return $e->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Invoice $Invoice)
    {
        $Invoice->update($request->validated());

        InvoiceItem::where("invoice_id", $Invoice->id)->delete();

        $items = array_map(function ($item) use ($Invoice) {
            $item['invoice_id'] = $Invoice->id;

           
            return $item;
        }, $request->items);

        InvoiceItem::insert($items);

        return $Invoice;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $Invoice)
    {
        $Invoice->delete();

        return response()->noContent();
    }
}
