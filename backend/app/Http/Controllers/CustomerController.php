<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request, Customer $model)
    {
        return $model->paginate($request->per_page);
    }

    public function store(Request $request)
    {
        $customer_info = [
            'name'            => $request->first_name . ' ' . $request->last_name,
            'first_name'      => $request->first_name,
            'last_name'       =>  $request->last_name,
            'contact_no'      => $request->contact_no,
            'email'           => $request->email,
            'id_card_type_id' => $request->id_card_type_id,
            'id_card_no'      => $request->id_card_no,
            'car_no'          => $request->car_no,
            'no_of_adult'     => $request->no_of_adult,
            'no_of_child'     => $request->no_of_child,
            'no_of_baby'      => $request->no_of_baby,
            'address'         => $request->address,
        ];

        return Customer::create($customer_info);
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
}
