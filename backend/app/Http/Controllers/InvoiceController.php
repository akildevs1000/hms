<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\ValidationRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Template;
use Barryvdh\DomPDF\Facade\Pdf;

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

            $data["quotation_id"] = $request->quotation_id ?? 0;

            $invoice = Invoice::create($data);

            $invoice->load('customer');

            // $fields = [
            //     "title"     => $invoice->customer->title,
            //     "full_name" => $invoice->customer->first_name . " " . $invoice->customer->last_name,
            //     "check_in"  => date('d-M-y', strtotime($invoice->arrival_date)),
            //     "check_out" => date('d-M-y', strtotime($invoice->departure_date)),
            //     "rooms_type" => $invoice->rooms_type,
            //     "email" => $invoice->customer->email,
            //     "whatsapp" => $invoice->customer->whatsapp,
            // ];

            // $this->sendMailIfRequired(Template::QUOTATION_CREATE, $fields);
            // $this->sendWhatsappIfRequired(Template::QUOTATION_CREATE, $fields);


            return $invoice;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function roomInvoicePrint($id)
    {
        $quotation = Invoice::with("company", "customer")->where("invoice_type", "room")->find($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));

        return Pdf::loadView('invoice.room', compact("quotation"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function hallInvoicePrint($id)
    {
        $quotation = Invoice::with("company", "customer")->where("invoice_type", "hall")->find($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));

        return Pdf::loadView('invoice.hall', compact("quotation"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }
}
