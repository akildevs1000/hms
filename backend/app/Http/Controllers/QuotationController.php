<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotation\ValidationRequest;
use App\Models\Customer;
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
    public function getCustomerFields()
    {
        return [
            "title",
            "whatsapp",
            "first_name",
            "last_name",
            "contact_no",
            "email",
            "company_id",
            "country",
            "state",
            "city",
            "zip_code",
        ];
    }
    public function store(ValidationRequest $request)
    {
        // Quotation::truncate();
        // QuotationItem::truncate();

        try {
            DB::beginTransaction();
            $data = $request->validated();

            $data["bank_details"] = "null";
            $data["terms_and_conditions"] = "null";

            $customerData = $request->input('customer');
            $filteredData = collect($customerData)->only($this->getCustomerFields())->toArray();


            $data["customer_id"] = $this->customerStore($filteredData);


            $lastquotationId = Quotation::max("id") + 1;

            $ref_no = $lastquotationId < 1000 ? $lastquotationId + 1000 : $lastquotationId;

            $data["ref_no"] = "QTN-$ref_no";

            $quotation =  Quotation::create($data);

            $items = array_map(function ($item) use ($quotation) {

                unset($item["extras"]);
                unset($item["audio"]);
                unset($item["cleaning"]);
                unset($item["electricity"]);
                unset($item["extra_booking_hours_charges"]);
                unset($item["generator"]);
                unset($item["projector"]);
                unset($item["price_with_meal"]);

                $item["early_check_in"] = 0;
                $item["late_check_out"] = 0;
                $item["bed_amount"] = 0;


                $item['quotation_id'] = $quotation->id;
                $item['created_at'] = now();
                return $item;
            }, $request->items);

            QuotationItem::insert($items);

            // $quotation = Quotation::with(["customer", "items"])->first();

            // $fields = [
            //     "title"     => $quotation->customer->title,
            //     "full_name" => $quotation->customer->first_name . " " . $quotation->customer->last_name,
            //     "check_in"  => date('d-M-y', strtotime($quotation->arrival_date)),
            //     "check_out" => date('d-M-y', strtotime($quotation->departure_date)),
            //     "rooms_type" => $quotation->rooms_type,
            //     "email" => $quotation->customer->email,
            //     "whatsapp" => $quotation->customer->whatsapp,
            // ];

            // $this->sendMailIfRequired(Template::QUOTATION_CREATE, $fields);
            // $this->sendWhatsappIfRequired(Template::QUOTATION_CREATE, $fields);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception or handle it as necessary
            return response()->json($e->getMessage(), 500);
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

    public function customerStore($customer)
    {
        try {
            $isExistCustomer = false;
            if (!is_null($customer['contact_no'])) {
                $isExistCustomer = Customer::whereContactNo($customer['contact_no'])->whereCompanyId($customer['company_id'])->first();
            }
            $id = "";
            if ($isExistCustomer) {
                $id = $isExistCustomer->id;
                $isExistCustomer->update($customer);
            } else {

                $record = Customer::create($customer);
                $id = $record->id ?? 0;
            }
            return $id;
            return $this->response('Customer successfully added.', $id, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
