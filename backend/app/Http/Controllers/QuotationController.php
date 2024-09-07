<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotation\ValidationRequest;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Template;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function roomQuotaionPrint($id)
    {
        $quotation = Quotation::with("company", "customer")->where("type", "room")->find($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));

        return Pdf::loadView('quotation.room', compact("quotation"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function hallQuotaionPrint($id)
    {
        $quotation = Quotation::where("type", "hall")->find($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "unit_price"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "qty"));
        $quotation->total_booking_hours = array_sum(array_column($quotation->items, "total_booking_hours"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));
        $function_names = join(",", array_column($quotation->items, "function_name"));
        $quotation->function_names =  $function_names == "" ?  "---" : $function_names;
        return Pdf::loadView('quotation.hall', compact("quotation"))
            // ->setPaper('a4', 'landscape')
            ->setPaper('a4', 'portrait')
            ->stream();
    }

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
    public function index(Request $request)
    {
        $model = Quotation::query();
        $model->with(["customer", "followups", "status"]);
        $model->where("type", request("type", "room"));

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            $model->whereDate('created_at', ">=", $from);
            $model->whereDate('created_at', "<=", $to);
        }

        return $model->where("company_id", request("company_id", 0))
            ->orderBy("id", "desc")
            ->paginate(request("per_page", 50));
    }

    public function quotationRoomSearch($search)
    {
        $query = Quotation::query();

        $query->with(["customer", "followups", "status"]);
        // $query->where("type", request("type", "room"));
        $query->where(function ($q) use ($search) {
            $q->where("ref_no", env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
            $q->orWhereHas('customer', function ($query) use ($search) {
                $query->where('first_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
                $query->orWhere('last_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%'); // Wildcard search for 'first_name'
            });
        });

        // ->filter($key)
        return $query->where("company_id", request("company_id", 0))
            ->orderBy("id", "desc")
            ->paginate(request("per_page", 50));
    }

    public function quotationHallSearch($search)
    {
        $query = Quotation::query();

        $query->with(["customer", "followups", "status"]);
        $query->where("type", request("type", "hall"));
        $query->where(function ($q) use ($search) {
            $q->where("ref_no", env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
            $q->orWhereHas('customer', function ($query) use ($search) {
                $query->where('first_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
                $query->orWhere('last_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%'); // Wildcard search for 'first_name'
            });
        });

        // ->filter($key)
        return $query->where("company_id", request("company_id", 0))
            ->orderBy("id", "desc")
        ->paginate(request("per_page", 50));
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

        try {
            $data = $request->validated();

            $data["bank_details"] = "null";
            $data["terms_and_conditions"] = "null";

            $customerData = $request->input('customer');
            $filteredData = collect($customerData)->only($this->getCustomerFields())->toArray();

            $data["customer_id"] = $this->customerStore($filteredData);

            $lastquotationId = Quotation::max("id") + 1;

            $ref_no = $lastquotationId < 1000 ? $lastquotationId + 1000 : $lastquotationId;

            $data["ref_no"] = "QTN-$ref_no";

            Quotation::create($data);

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
     * @param  \App\Models\Quotation  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Quotation $Quotation)
    {
        $data = $request->validated();
        $customerData = $request->input('customer');
        $filteredData = collect($customerData)->only($this->getCustomerFields())->toArray();
        $data["customer_id"] = $this->customerStore($filteredData);
        $Quotation->update($data);
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
