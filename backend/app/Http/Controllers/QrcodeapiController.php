<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Company;
use App\Models\HotelFoodItems;
use App\Models\HotelFoodTimings;
use App\Models\HotelOrdersFood;
use Illuminate\Http\Request;

class QrcodeapiController extends Controller
{
    public function getCheckInCustomerDetails(Request $request)
    {
        // Determine the date based on the current time
        $date = date('Y-m-d');
        if (date('H') < 11) {
            $date = date('Y-m-d', strtotime($request->date ?? $date . ' -1 day'));
        }

        // Initialize the model with relationships
        $model = BookedRoom::with(['customer']);

        // Find the latest booking for the room, based on request parameters
        $bookedRoom = $model
            ->where('booking_status', 2)
            ->where('check_in', '<=', $date . ' ' . date('H:i:s'))
            ->Where('check_out', '>=', $date . ' ' . date('H:i:s'))
            ->where('company_id', $request->company_id ?? 0)
            ->where('room_id', $request->room_id ?? 0)
            ->orderBy('created_at', 'desc') // Adjust if another date column is more appropriate
            ->first();

        // Check if a valid booking not  found
        if (!$bookedRoom) {
            return $this->response('Check-in Details are not Found. Please try again', null, false);
        }

        // Generate and send OTP if requested
        if ($request->filled('otp') && $request->otp === "1") {
            $otp = rand(1000, 9999);
            $bookedRoom->update(['whatsapp_otp' => $otp]);

            $dataOtp = [
                'mobile' => $bookedRoom->customer['whatsapp'],
                'otp' => $otp,
                'name' => $bookedRoom->customer['title']
            ];

            // Send OTP notification if in production environment
            if (env('APP_ENV') == 'production') {
                (new WhatsappNotificationController)->hotelMenuOTP($dataOtp, $request->company_id);
            }
        }

        // Return response with booking details
        return $this->response('Success', $bookedRoom, true);
    }
    public function generateHotelFoodOTP(Request $request)
    { // Determine the date based on the current time
        $date = date('Y-m-d');
        if (date('H') < 11) {
            $date = date('Y-m-d', strtotime($request->date ?? $date . ' -1 day'));
        }

        // Initialize the model with relationships
        $model = BookedRoom::with(['customer']);

        // Find the latest booking for the room, based on request parameters
        $bookedRoom = $model
            ->where('booking_status', 2)
            // ->where('check_in', '<=', $date . ' ' . date('H:i:s'))
            // ->Where('check_out', '>=', $date . ' ' . date('H:i:s'))
            ->where('company_id', $request->company_id ?? 0)
            ->where('room_id', $request->room_id ?? 0)
            ->orderBy('created_at', 'desc') // Adjust if another date column is more appropriate
            ->first();

        // Check if a valid booking not  found
        if (!$bookedRoom) {
            return $this->response('Check-in Details are not Found. Please try again', null, false);
        }
        $otp = rand(1000, 9999);
        $bookedRoom->update(['whatsapp_otp' => $otp]);

        $dataOtp = [
            'mobile' => $bookedRoom->customer['whatsapp'],
            'otp' => $otp,
            'name' => $bookedRoom->customer['title']
        ];

        // Send OTP notification if in production environment
        if (env('APP_ENV') == 'production') {
            (new WhatsappNotificationController)->hotelMenuOTP($dataOtp, $request->company_id);
        }
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
                    $q->where('name', env("WILD_CARD") ?? "ILIKE",   '%' . $request->item_name_search . '%');
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
            $orders = $model->get();
            $groupedOrders = [];

            foreach ($orders as $order) {
                $datetime = $order->request_datetime;

                if (!isset($groupedOrders[$datetime])) {
                    $groupedOrders[$datetime] = [];
                }

                $groupedOrders[$datetime][] = $order;
            }

            return $groupedOrders;
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }
    public function updateCheckoutByGuest(Request $request)
    {


        if ($request->booking_id && $request->room_id) {
            $model = BookedRoom::find($request->booking_room_id);
            if ($model) {
                $checkout_guest_request = date("Y-m-d H:i:s");
                $data = ["checkout_guest_request" => $checkout_guest_request];

                $results = $model->update($data);
                if ($results)

                    return $this->response("Checkout request is Received.", $results, true);
            }
        }

        return $this->response("Request is invalid", null, false);
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
    public function checkoutProcess(Request $request)
    {
        $controller = new PostingController();
        foreach ($request->postings as $key => $post) {
            $request = new Request([
                "tax_type" => "Food",
                "item" => "Dosa",
                "qty" => "5",
                "single_amt" => "100",
                "amount" => 500,
                "tax" => 25,
                "amount_with_tax" => 525,
                "sgst" => 12.5,
                "cgst" => 12.5,
                "bill_no" => 10,
                "booked_room_id" => 1037,
                "company_id" => 3,
                "booking_id" => 676,
                "room_id" => 70,
                "room" => "104",
                "user_id" => 4
            ]);

            $response = $controller->store($request);
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

    //     {
    //   "tax_type": "Food",
    //   "item": "Dosa",
    //   "qty": "5",
    //   "single_amt": "100",
    //   "amount": 500,
    //   "tax": 25,
    //   "amount_with_tax": 525,
    //   "sgst": 12.5,
    //   "cgst": 12.5,
    //   "bill_no": 10,
    //   "booked_room_id": 1037,
    //   "company_id": 3,
    //   "booking_id": 676,
    //   "room_id": 70,
    //   "room": "104",
    //   "user_id": 4
    // }

    //posting?company_id=3
}
