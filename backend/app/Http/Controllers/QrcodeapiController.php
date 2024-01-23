<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Holiday;
use App\Models\HotelFoodItems;
use App\Models\HotelFoodTimings;
use App\Models\HotelOrdersFood;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TaxSlabs;
use App\Models\Weekend;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QrcodeapiController extends Controller
{


    public function getCheckInCustomerDetails(Request $request)
    {
        $date = date('Y-m-d'); //$request->date;
        if (date('H') <= 11) {
            $date = date('Y-m-d', strtotime($request->date, '-1 day'));
        }


        $model = BookedRoom::with(["customer"]);
        $bookedRoomIds = $model
            ->where('check_in', '<=', $date . ' ' . date('H:i:s'))
            ->Where('check_out', '>=', $date . ' ' . date('H:i:s'))
            // ->whereHas('booking', function ($q) use ($request) {
            //     $q->where('booking_status', 2);
            //     $q->where('company_id', $request->company_id);
            // })
            ->where('booking_status', 2)
            ->where('company_id', $request->company_id)
            ->Where('room_id',   $request->room_id)
            ->get()->first();

        if ($bookedRoomIds) {
            if ($request->filled("otp")) {

                if ($request->otp === "1") {


                    $opt = rand(1000, 9999);
                    $opt = $opt;
                    $bookedRoomIds['whatsapp_otp'] = $opt;
                    $model->update(["whatsapp_otp" => $opt]);

                    $data_otp['mobile'] = $bookedRoomIds->customer['whatsapp'];
                    $data_otp['otp'] = $opt;
                    $data_otp['name'] = $bookedRoomIds->customer['title'];
                    if (env("APP_ENV") == "production") (new WhatsappNotificationController)->hotelMenuOTP($data_otp, $request->company_id);
                }
            }
        }
        if (!isset($bookedRoomIds['id'])) {
            return $this->response('Check-in Details are not Found. Please try again', null, false);
        }
        return $this->response('Success', $bookedRoomIds, true);
    }

    public function getCustomerMenu(Request $request)
    {
        $model = HotelFoodTimings::where("company_id", $request->company_id)->orderBY("display_order", "asc");
        $timings = $model->get();
        $final = [];
        foreach ($timings as $key => $value) {
            $model2 = HotelFoodItems::where("company_id", $request->company_id)
                ->whereHas('timings', function ($q) use ($value) {
                    $q->Where('category_id',  $value['id']);
                })
                ->when($request->filled('item_name_search'), function ($q) use ($request) {
                    $q->where('name', "ILIKE",   '%' . $request->item_name_search . '%');
                })

                ->orderBY("display_order", "asc")->get();;
            $row = [];
            foreach ($model2 as $item) {
                $row[$item['category']['name']][] = $item;
            }
            if (count($row))
                $final[$value['name']] = $row;
        }

        return  $final;
    }

    public function addFoodOrderItems(Request $request)
    {

        try {
            $company_food_tax = Company::whereId($request->company_id)->pluck('food_tax')->first();

            $booking_rooms_id = BookedRoom::where("company_id", $request->company_id)
                ->where("booking_id", $request->booking_id)
                ->where("room_id", $request->room_id)
                ->pluck('id')->first();

            $data["company_id"] = $request->company_id;
            $data["booking_id"] = $request->booking_id;
            $data["room_id"] = $request->room_id;
            $data["request_datetime"] = date('Y-m-d H:i:s');

            $data["booking_rooms_id"] = $booking_rooms_id;

            foreach ($request->cart_items as $items) {
                $data["food_item_id"] = $items['id'];
                $data["food_price"] = $items['amount'];

                $data["qty"] = $items['qty'];


                $total = $items['amount'] * $items['qty'];
                $tax_total = $total * $company_food_tax / 100;
                $data["food_sgst"] =  $tax_total / 2;
                $data["food_cgst"] = $tax_total / 2;
                $data["food_total"] =  $total + $tax_total;





                HotelOrdersFood::create($data);
            }

            return $this->response('Food Order is created Successfully', 200, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    public function getFoodOrderItems(Request $request)
    {
        try {
            $model = HotelOrdersFood::with(["room", "food", "booking"]);


            $model->where('company_id', $request->company_id)
                ->where('room_id', $request->room_id)
                ->where('booking_id', $request->booking_id);

            $model = $model->orderBy('request_datetime', "desc");
            return $model->get();
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    public function cancelFoodOrderItem(Request $request)
    {
        try {
            $data["status"] = 3; //cancel
            $data["reason_cancelled"] = "Customer canceled";
            $data["cancelled_datetime"] = date('Y-m-d H:i:s');
            $status = HotelOrdersFood::where("company_id", $request->company_id)->whereId($request->item_id)->where("status", 0)->update($data);
            return $this->response('Order Details are cancelled succesfully', $status, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    // public function getCheckInCustomerDetails(Request $request)
    // {
    //     return  $checkInRooms = BookedRoom::whereHas('booking', function ($q) use ($request) {
    //         $q->where('booking_status', '!=', 0);
    //         $q->where('company_id', 1);
    //         $q->where('booking_status', 2);
    //         $q->where(
    //             'room_id',
    //             1
    //         );
    //     })->get(["booking_id", "no_of_adult", "no_of_child", "no_of_baby"])->toArray();
    // }
}
