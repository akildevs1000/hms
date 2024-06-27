<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotation\ValidationRequest;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Template;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return Quotation::get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Quotation::with(["customer", "items", "followups", "status"])->paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        // Quotation::truncate();
        // QuotationItem::truncate();

        try {
            DB::beginTransaction();
            $data = $request->validated();

            $data["bank_details"] = "null";
            $data["terms_and_conditions"] = "null";


            $lastquotationId = Quotation::max("id") + 1;

            $ref_no = $lastquotationId < 1000 ? $lastquotationId + 1000 : $lastquotationId;

            $data["ref_no"] = "QTN-$ref_no";

            $quotation =  Quotation::create($data);

            $items = array_map(function ($item) use ($quotation) {
                $item['quotation_id'] = $quotation->id;
                $item['created_at'] = now();
                return $item;
            }, $request->items);

            QuotationItem::insert($items);


            $quotation = Quotation::with(["customer", "items"])->first();

            $fields = [
                "title"     => $quotation->customer->title,
                "full_name" => $quotation->customer->first_name . " " . $quotation->customer->last_name,
                "check_in"  => date('d-M-y', strtotime($quotation->arrival_date)),
                "check_out" => date('d-M-y', strtotime($quotation->departure_date)),
                "rooms_type" => $quotation->rooms_type,
                "email" => $quotation->customer->email,
                "whatsapp" => $quotation->customer->whatsapp,
            ];

            $this->sendMailIfRequired(Template::QUOTATION_CREATE, $fields);
            $this->sendWhatsappIfRequired(Template::QUOTATION_CREATE, $fields);

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
     * @param  \App\Models\Quotation  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Quotation $Quotation)
    {
        $Quotation->update($request->validated());


        $items = array_map(function ($item) use ($Quotation) {
            $item['quotation_id'] = $Quotation->id;
            return $item;
        }, $request->items);
        QuotationItem::where("quotation_id", $Quotation->id)->delete();

        QuotationItem::insert($items);

        return $Quotation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quotation  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quotation $Quotation)
    {
        $Quotation->delete();

        return response()->noContent();
    }
}
