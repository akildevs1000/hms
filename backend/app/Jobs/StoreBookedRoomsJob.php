<?php

namespace App\Jobs;

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

            if ($this->data['company_id'] == 3) {
                foreach ($rooms as $room) {
                    $room['booking_id'] = $this->data['booking_id'];
                    $room['customer_id'] = $this->data['customer_id'];
                    $room['booking_status'] = $this->data['booking_status'];

                    $priceList = $room['priceList'];
                    unset($room['priceList'], $room['meal_name'], $room['total_price'], $room['room_type_object']);

                    $bookedRoomId = BookedRoom::create($room);
                    $orderRooms = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));

                    $singleDayDiscount = ($this->data['room_discount'] / count($priceList) / count($rooms));
                    $singleDayExtraAmount = ($this->data['room_extra_amount'] / count($priceList) / count($rooms));

                    foreach ($priceList as $list) {
                        $singleDayPrice = $list['price'];
                        $taxArray = $this->reCalculatePrice($list['price'] - $singleDayDiscount + $singleDayExtraAmount);

                        $price_adjusted_after_dsicount = $taxArray['basePrice'];
                        $list['tax'] = $taxArray['gstAmount'];
                        // Recalculation end

                        $orderRooms['price_adjusted_after_dsicount'] = $price_adjusted_after_dsicount;
                        $orderRooms['date'] = $list['date'];

                        $orderRooms['room_discount'] = $singleDayDiscount;
                        $orderRooms['after_discount'] = $list['price'] - $orderRooms['room_discount'] + $singleDayExtraAmount;
                        $price = $orderRooms['after_discount'];
                        $orderRooms['total_with_tax'] = $price;
                        $orderRooms['price'] = $price;

                        $orderRooms['days'] = 1;
                        $orderRooms['room_tax'] = $list['tax'];
                        $orderRooms['sgst'] = $list['tax'] / 2;
                        $orderRooms['cgst'] = $list['tax'] / 2;
                        $orderRooms['booked_room_id'] = $bookedRoomId->id;
                        $orderRooms['customer_id'] = $bookedRoomId->customer_id;
                        $orderRooms['meal'] = $bookedRoomId->meal;
                        $orderRooms['no_of_adult'] = $bookedRoomId->no_of_adult;
                        $orderRooms['no_of_child'] = $bookedRoomId->no_of_child;
                        $orderRooms['no_of_baby'] = $bookedRoomId->no_of_baby;
                        $orderRooms['food_plan_id'] = $bookedRoomId->food_plan_id;
                        $orderRooms['food_plan_price'] = $bookedRoomId->food_plan_price;
                        $orderRooms['extra_bed_qty'] = $bookedRoomId->extra_bed_qty;
                        $orderRooms['early_check_in'] = $bookedRoomId->early_check_in;
                        $orderRooms['late_check_out'] = $bookedRoomId->late_check_out;

                        $orderRooms['breakfast'] = $bookedRoomId->breakfast ?? 0;
                        $orderRooms['lunch'] = $bookedRoomId->lunch ?? 0;
                        $orderRooms['dinner'] = $bookedRoomId->dinner ?? 0;

                        $orderRooms['tariff'] = $list['day_type'] ?? "";
                        $orderRooms['day'] = $list['day']  ?? null;

                        OrderRoom::create($orderRooms);
                    }
                }
            } else {
                foreach ($rooms as $room) {
                    $room['booking_id'] = $this->data['booking_id'];
                    $room['customer_id'] = $this->data['customer_id'];
                    $room['booking_status'] = $this->data['booking_status'];

                    $priceList = $room['priceList'];
                    unset($room['priceList'], $room['meal_name'], $room['total_price'], $room['room_type_object']);

                    $bookedRoomId = BookedRoom::create($room);
                    $orderRooms = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));

                    $singleDayDiscount = ($this->data['room_discount'] / count($priceList) / count($rooms));
                    $singleDayExtraAmount = ($this->data['room_extra_amount'] / count($priceList) / count($rooms));

                    foreach ($priceList as $list) {
                        $singleDayPrice = $list['room_price'];
                        $taxArray = $this->reCalculatePrice($list['price'] - $singleDayDiscount + $singleDayExtraAmount);

                        $orderRooms['price_adjusted_after_dsicount'] = $taxArray['basePrice'];
                        $orderRooms['date'] = $list['date'];

                        $orderRooms['room_discount'] = $singleDayDiscount;
                        $orderRooms['after_discount'] = ($list['price'] - $orderRooms['room_discount']) + $singleDayExtraAmount;

                        $price = $orderRooms['after_discount'];

                        $orderRooms['total'] = $price + $bookedRoomId->food_plan_price;
                        $orderRooms['grand_total'] = $price + $bookedRoomId->food_plan_price;
                        $orderRooms['total_with_tax'] = $price;
                        $orderRooms['price'] = $price;
                        $orderRooms['days'] = 1;
                        $orderRooms['room_tax'] = $list['tax'];
                        $orderRooms['sgst'] = $list['tax'] / 2;
                        $orderRooms['cgst'] = $list['tax'] / 2;
                        $orderRooms['booked_room_id'] = $bookedRoomId->id;
                        $orderRooms['customer_id'] = $bookedRoomId->customer_id;
                        $orderRooms['meal'] = $bookedRoomId->meal;
                        $orderRooms['no_of_adult'] = $bookedRoomId->no_of_adult;
                        $orderRooms['no_of_child'] = $bookedRoomId->no_of_child;
                        $orderRooms['no_of_baby'] = $bookedRoomId->no_of_baby;
                        $orderRooms['food_plan_id'] = $bookedRoomId->food_plan_id;
                        $orderRooms['food_plan_price'] = $bookedRoomId->food_plan_price;
                        $orderRooms['extra_bed_qty'] = $bookedRoomId->extra_bed_qty;
                        $orderRooms['early_check_in'] = $bookedRoomId->early_check_in;
                        $orderRooms['late_check_out'] = $bookedRoomId->late_check_out;

                        $orderRooms['breakfast'] = $bookedRoomId->breakfast ?? 0;
                        $orderRooms['lunch'] = $bookedRoomId->lunch ?? 0;
                        $orderRooms['dinner'] = $bookedRoomId->dinner ?? 0;

                        $orderRooms['tariff'] = $list['day_type'] ?? "";
                        $orderRooms['day'] = $list['day']  ?? null;

                        OrderRoom::create($orderRooms);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::alert(json_encode($e->getMessage()));
        }
    }

    public function reCalculatePrice($finalAmountWithDiscount)
    {
        $tax = 12;
        if ($finalAmountWithDiscount >= 2800) {
            $tax = 18;
        } else if ($finalAmountWithDiscount >= 9600) {
            $tax = 28;
        }

        $basePrice = ($finalAmountWithDiscount * 100) / (100 + $tax);
        $gstAmount = $finalAmountWithDiscount - $basePrice;

        return ["basePrice" => round($basePrice, 2), "gstAmount" => round($gstAmount, 2), "tax" => $tax];
    }
}
