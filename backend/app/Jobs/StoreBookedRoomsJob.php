<?php

namespace App\Jobs;

use App\Http\Controllers\BookingController;
use App\Models\BookedRoom;
use App\Models\OrderRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreBookedRoomsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $rooms = $this->data['selectedRooms'];

            foreach ($rooms as $room) {
                $room['booking_id'] = $this->data['booking_id'];
                $room['customer_id'] = $this->data['customer_id'];
                $room['booking_status'] = $this->data['booking_status'];

                $priceList = $room['priceList'];
                $meal_name = $room['meal_name'];
                $total_price = $room['total_price'];
                unset($room['priceList'], $room['meal_name'], $room['total_price'], $room['room_type_object']);

                $bookedRoomId = BookedRoom::create($room);

                $eachRoomFoodPlanPrice = $bookedRoomId->food_plan_price;
                $eachRoomBedAmount = $bookedRoomId->bed_amount;
                $eachRoomEarlyCheckIn = $bookedRoomId->early_check_in;
                $eachRoomLateCheckOut = $bookedRoomId->late_check_out;
                // $additionalCharges = $eachRoomFoodPlanPrice + $eachRoomBedAmount + $eachRoomEarlyCheckIn + $eachRoomLateCheckOut;

                $orderRooms = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));

                // $singleDayDiscount = ($this->data['room_discount'] / count($priceList) / count($rooms));
                // $singleDayExtraAmount = ($this->data['room_extra_amount'] / count($priceList) / count($rooms));

                foreach ($priceList as $list) {

                    $orderRooms['booked_room_id'] = $bookedRoomId->id;
                    $orderRooms['breakfast'] = $bookedRoomId->breakfast ?? 0;
                    $orderRooms['lunch'] = $bookedRoomId->lunch ?? 0;
                    $orderRooms['dinner'] = $bookedRoomId->dinner ?? 0;


                    $orderRooms['days'] = 1;
                    $orderRooms['customer_id'] = $room['customer_id'];
                    $orderRooms['meal'] = $meal_name;
                    $orderRooms['no_of_adult'] = $room['no_of_adult'];
                    $orderRooms['no_of_child'] = $room['no_of_child'];
                    $orderRooms['no_of_baby'] = $room['no_of_baby'];
                    $orderRooms['food_plan_id'] = $room['food_plan_id'];
                    $orderRooms['room_discount'] = 0;
                    $orderRooms['after_discount'] = 0;
                    $orderRooms['date'] = $list['date'];
                    $orderRooms['tariff'] = $list['day_type'] ?? "";
                    $orderRooms['day'] = $list['day']  ?? null;


                    $orderRooms['cgst'] = $room['cgst'];
                    $orderRooms['sgst'] = $room['sgst'];
                    $orderRooms['room_tax'] = $room['room_tax'];




                    //  "room_no": "302",
                    // "room_id": 99,
                    // "date": "2024-12-10",
                    // "price": 5320,
                    // "day_type": "weekday",
                    // "day": "Tuesday",
                    // "tax": 570,

                    // "discount": 0,
                    // "meal": "------",
                    // "meal_name": "Break Fast",
                    // "food_plan_price": 275,
                    // "breakfast": 1,
                    // "lunch": 0,
                    // "dinner": 0,
                    // "room_type": "Grand",
                    // "no_of_adult": 1,
                    // "no_of_child": 0,
                    // "early_check_in": 100,
                    // "late_check_out": 100,
                    // "bed_amount": 500,
                    // "extra_bed_qty": 1,
                    // "total_price": 6295


                    // "room_price": "4750.00",


                    $orderRooms['food_plan_price'] = $eachRoomFoodPlanPrice;
                    $orderRooms['bed_amount'] = $eachRoomBedAmount;
                    $orderRooms['early_check_in'] = $eachRoomEarlyCheckIn;
                    $orderRooms['late_check_out'] = $eachRoomLateCheckOut;


                    $orderRooms['price'] = $list['room_price'];
                    $orderRooms['total_with_tax'] = $list['price'];
                    $orderRooms['total'] = $list['total_price'];
                    $orderRooms['grand_total'] = $list['total_price'];


                    // recalculate
                    $BookingObj = new BookingController();
                    $room_tax =   $BookingObj->getTaxSlab(($list['total_price'] + 900), $bookedRoomId->company_id);
                    $roomBasePrice = ($list['total_price'] * 100) / (100 + $room_tax);
                    $roomGSTAmount = $list['total_price'] - $roomBasePrice;
                    $orderRooms['price'] = $roomBasePrice;
                    $orderRooms['cgst'] = $roomGSTAmount / 2;
                    $orderRooms['sgst'] = $roomGSTAmount / 2;
                    $orderRooms['room_tax'] = $roomGSTAmount;

                    OrderRoom::create($orderRooms);
                }
            }
        } catch (\Exception $e) {
            Log::alert(json_encode($e->getMessage()));
        }
    }
}
