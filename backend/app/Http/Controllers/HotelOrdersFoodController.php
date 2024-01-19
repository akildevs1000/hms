<?php

namespace App\Http\Controllers;

//use App\Http\Requests\Allowance\StoreRequest;

use App\Http\Requests\HotelOrdersFood\StoreRequest;
use App\Http\Requests\HotelOrdersFood\UpdateRequest;
use App\Models\Company;
use App\Models\HotelFoodItems;
use App\Models\HotelFoodTimings;
use App\Models\HotelOrdersFood;

use Illuminate\Http\Request;


class HotelOrdersFoodController extends Controller
{
    public function index(Request $request)
    {
        $model = HotelOrdersFood::with(["room", "food", "booking"]);


        $model = $model->where('company_id', $request->company_id);


        if ($request->filled('room_id')) {
            $model->Where('room_id',   $request->room_id);
        }
        if ($request->filled('food_item_id')) {
            $model->Where('food_item_id',   $request->item_id);
        }
        if ($request->filled('status')) {
            $model->Where('status',   $request->status);
        }
        if ($request->filled('from_date')) {
            $model->Where('request_datetime', '>=',  $request->from_date . ' 00:00:00');
        }
        if ($request->filled('to_date')) {
            $model->Where('request_datetime', '<=',  $request->to_date . ' 23:59:59');
        }


        if ($request->filled('filter_room_id')) {
            $model->Where('room_id',   $request->filter_room_id);
        }
        if ($request->filled('filter_request_datetime')) {
            $model->Where('request_datetime',   $request->filter_request_datetime);
        }


        if ($request->filled('is_non_veg')) {
            $model->whereHas('food', function ($q) use ($request) {
                $q->Where('is_non_veg', $request->is_non_veg);
            });
        }
        $model->when($request->filled('sortBy'), function ($q) use ($request) {
            $sortDesc = $request->input('sortDesc');
            if (strpos($request->sortBy, '.')) {
                if ($request->sortBy == 'item.name') {
                    $q->orderBy(HotelFoodItems::select("name")->whereColumn("hotel_food_items.id", "hotel_orders_food.food_item_id"), $sortDesc == 'true' ? 'desc' : 'asc');
                } else if ($request->sortBy == 'item.name') {
                    $q->orderBy(HotelFoodItems::select("name")->whereColumn("hotel_food_items.id", "hotel_orders_food.food_item_id"), $sortDesc == 'true' ? 'desc' : 'asc');
                }
            } else {
                $q->orderBy($request->sortBy . "", $sortDesc == 'true' ? 'desc' : 'asc'); {
                }
            }
        });



        return $model->paginate($request->per_page);
    }
    public function updateHotelOrderToPreparing(Request $request)
    {

        try {
            $data["status"] = 1; //preparing
            $status = HotelOrdersFood::where("company_id", $request->company_id)->whereId($request->hotel_order_id)->where("status", 0)->update($data);
            return $this->response('Order Details are updated succesfully', $status, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }
    public function updateHotelOrderToDelivered(Request $request)
    {


        //return $request->all();
        try {
            $company_food_tax = Company::whereId($request->company_id)->pluck('food_tax')->first();
            $data["status"] = 2; //delivered
            $status = HotelOrdersFood::where("company_id", $request->company_id)->whereId($request->hotel_order_id)->where("status", 1)->update($data);

            //insert into postings 
            $posting["item"] =  $request->item["food"]["name"];
            $posting["qty"] =  $request->item["qty"];
            $posting["amount"] = $request->item["food_price"] * $request->item["qty"];
            $posting["bill_no"] = $request->item["id"];
            $posting["amount_with_tax"] = $request->item["food_total"];
            $posting["tax"] = $request->item["food_sgst"] + $request->item["food_cgst"];
            $posting["sgst"] = $request->item["food_sgst"];
            $posting["cgst"] = $request->item["food_cgst"];
            $posting["tax_type"] = $company_food_tax;
            $posting["single_amt"] = $request->item["food_price"];
            $posting["booked_room_id"] = $request->item["booking_rooms_id"];
            $posting["company_id"] = $request->company_id;
            $posting["booking_id"] = $request->item["booking_id"];
            $posting["room_id"] = $request->item["room_id"];
            $posting["room"] = $request->item["room"]["room_no"];
            $posting["user_id"] = 2;

            $postRequst = Request::create('/posting', 'POST',  $posting);
            $data = (new PostingController)->store($postRequst);

            return $this->response('Order Details are updated succesfully', $status, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    public function updateHotelOrderToCancel(Request $request)
    {

        $data["status"] = 3; //cancel
        $data["reason_cancelled"] = $request->reason_cancelled;
        $data["cancelled_datetime"] = date('Y-m-d H:i:s');
        $status = HotelOrdersFood::where("company_id", $request->company_id)->whereId($request->hotel_order_id)->where("status", 0)->update($data);
        return $this->response('Order Details are updated succesfully', $status, true);
    }
    public function getDashbordStatistics(Request $request)
    {
        $model = HotelOrdersFood::with(["room", "food", "booking"]);


        $model = $model->where('company_id', $request->company_id);


        if ($request->filled('room_id')) {
            $model->Where('room_id',   $request->room_id);
        }
        if ($request->filled('food_item_id')) {
            $model->Where('food_item_id',   $request->item_id);
        }
        if ($request->filled('status')) {
            $model->Where('status',   $request->status);
        }
        if ($request->filled('from_date')) {
            $model->Where('request_datetime', '>=',  $request->from_date . ' 00:00:00');
        }
        if ($request->filled('to_date')) {
            $model->Where('request_datetime', '<=',  $request->to_date . ' 23:59:59');
        }

        if ($request->filled('is_non_veg')) {
            $model->whereHas('food', function ($q) use ($request) {
                $q->Where('is_non_veg', $request->is_non_veg);
            });
        }

        return
            [
                "total" => $model->clone()->count(),
                "new" => $model->clone()->where("status", 0)->count(),
                "preparing" =>  $model->clone()->where("status", 1)->count(),
                "completed" =>  $model->clone()->where("status", 2)->count(),
                "cancelled" =>  $model->clone()->where("status", 3)->count(),

            ];
    }

    // public function getCustomerMenu(Request $request)
    // {

    //     // $model = HotelFoodTimings::with(["food"])->where("company_id", $request->company_id);

    //     // return $model->get();;

    //     $model = HotelFoodItems::where("company_id", $request->company_id)->orderBY("display_order", "asc");


    //     // $currentTimeHour = date('H');

    //     // $model->whereHas('timings', function ($q) use ($request) {
    //     //     $q->Where('start_hour', '>', $request->currentTimeHour);
    //     //     $q->Where('end_hour', '<=', $request->currentTimeHour);
    //     // });

    //     $data = $model->get();;
    //     //return $data;
    //     $final = [];
    //     foreach ($data as $item) {

    //         foreach ($item['timings'] as $timing) {
    //             if ($item->category && $timing->timings)
    //                 $final[$timing->timings->name][$item->category->name][] = $item;
    //         }
    //     }
    //     // $displayOrder = array_column($final, 'display_order');
    //     // array_multisort($displayOrder, $final);



    //     return $final;
    // }

    public function update(UpdateRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {


                $status = HotelOrdersFood::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Order Details are updated succesfully', $status, true);
                } else {
                    return $this->response('Order Details are not Updated', $status, false);
                }
            } else {
                return $this->response('Error Occured', $data, false);
            }
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }



    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $record = HotelOrdersFood::create($data);

                if ($record) {
                    return $this->response('Order details are successfully created', $record, true);
                } else {
                    return $this->response('Order details not created', $record, false);
                }
            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show($id)
    {
        return HotelOrdersFood::where('id', $id)->first();
    }
    public function destroy($id)
    {

        return $this->response('Record    cannot   delete .', null, true);
        // if (HotelOrdersFood::find($id)->delete()) {

        //     return $this->response('Record    successfully deleted.', null, true);
        // } else {
        //     return $this->response('Record   cannot delete.', null, false);
        // }
    }
}
