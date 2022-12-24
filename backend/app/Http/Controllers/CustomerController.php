<?php

namespace App\Http\Controllers;

use App\Models\Booking;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\Customer\StoreRequest;

class CustomerController extends Controller
{
    public function index(Request $request, Customer $model)
    {
        return $model
            ->where('company_id', $request->company_id)
            ->paginate($request->per_page);
    }

    public function store(StoreRequest $request)
    {
        try {

            $customer =   Customer::whereContactNo($request->contact_no)->first();
            $id = "";

            if ($customer) {
                $id = $customer->id;
            } else {
                $record = Customer::create($request->validated());
                $id = $record->id;
            }
            return $this->response('Customer successfully added.', $id, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function search(Request $request, $key)
    {
        $model  = Customer::query();
        $fields = [
            'name',
            'contact_no',
            'email',
            "id_card_type_id",
            "id_card_no",
            'car_no',
            "no_of_adult",
            "no_of_child",
            "no_of_baby",
            "address",
        ];
        $model = $this->process_search($model, $key, $fields);
        return $model->where("company_id", $request->company_id)->paginate($request->perPage);
    }

    public function getCustomer($id)
    {
        $data =  Customer::where('contact_no', $id)->first();

        if ($data) {
            // if (str_word_count($data->name) > 1) {
            //     $name = $data->name;
            //     $data->first_name =   explode(" ", $name)[0];
            //     $data->last_name =   explode(" ", $name)[1];
            // } else {
            //     $data->first_name = $data->name;
            // }
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
        return Customer::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0)
                ->where('booking_status', '<=', 3);
        })->with(
            ['booking' => [
                'bookedRooms',
                'payments',
            ]]
        )
            ->find($id);
    }
}