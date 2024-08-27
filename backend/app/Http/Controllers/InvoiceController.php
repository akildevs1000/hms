<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\ValidationRequest;
use App\Models\Customer;
use App\Models\Invoice;

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
        return Invoice::with(["customer", "quotation"])->paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        try {
            $data = $request->validated();

            $data["bank_details"] = "null";
            $data["terms_and_conditions"] = "null";

            $customerData = $request->input('customer');
            $filteredData = collect($customerData)->only($this->getCustomerFields())->toArray();

            $data["customer_id"] = $this->customerStore($filteredData);

            $lastInvoiceId = Invoice::max("id") + 1;

            $ref_no = $lastInvoiceId < 1000 ? $lastInvoiceId + 1000 : $lastInvoiceId;

            $data["ref_no"] = "INV-$ref_no";

            $data["quotation_id"] = $request->id ?? 0;

            Invoice::create($data);

            // $quotation = Quotation::with("customer")->first();

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


            return true;
        } catch (\Exception $e) {
            // Log the exception or handle it as necessary
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, $id)
    {
        $data = $request->validated();
        $customerData = $request->input('customer');
        $filteredData = collect($customerData)->only($this->getCustomerFields())->toArray();
        $data["customer_id"] = $this->customerStore($filteredData);
        return Invoice::where("id", $id)->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Invoice::where("id", $id)->delete();

        return response()->noContent();
    }

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
