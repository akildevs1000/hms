<?php

namespace App\Jobs;

use App\Http\Controllers\BookingController;
use App\Models\BookedRoom;
use App\Models\Company;
use App\Models\OrderRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log as Logger;



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


            $company_food_tax = Company::whereId($this->data['company_id'])->pluck('food_tax')->first();
            $rooms = $this->data['selectedRooms'];
            Logger::channel("custom")->error("inv_room_tax_per: " . json_encode($this->data));

            foreach ($rooms as $room) {
                $room['booking_id'] = $this->data['booking_id'];
                $room['customer_id'] = $this->data['customer_id'];
                $room['booking_status'] = $this->data['booking_status'];

                $priceList = $room['priceList'];
                $meal_name = $room['meal_name'];
                $total_price = $room['total_price'];
                unset($room['priceList'], $room['meal_name'], $room['total_price'], $room['room_type_object']);

                $bookedRoomId = BookedRoom::create($room);

                // $eachRoomFoodPlanPrice = $bookedRoomId->food_plan_price;
                $eachRoomBedAmount = $bookedRoomId->bed_amount;
                $eachRoomEarlyCheckIn = $bookedRoomId->early_check_in;
                $eachRoomLateCheckOut = $bookedRoomId->late_check_out;
                // $additionalCharges = $eachRoomFoodPlanPrice + $eachRoomBedAmount + $eachRoomEarlyCheckIn + $eachRoomLateCheckOut;

                $orderRooms = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));

                $singleDayDiscount = ($this->data['room_discount'] / count($priceList) / count($rooms));
                $singleDayExtraAmount = ($this->data['room_extra_amount'] / count($priceList) / count($rooms));
                $eachRoomFoodPlanPrice = $bookedRoomId->food_plan_price; // ($bookedRoomId->food_plan_price / count($priceList) / count($rooms));





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
                    $orderRooms['room_discount'] = $singleDayDiscount;

                    $orderRooms['date'] = $list['date'];
                    $orderRooms['tariff'] = $list['day_type'] ?? "";
                    $orderRooms['day'] = $list['day']  ?? null;

                    $orderRooms['cgst'] = $room['cgst'];
                    $orderRooms['sgst'] = $room['sgst'];
                    $orderRooms['room_tax'] = $room['room_tax'];


                    $orderRooms['food_plan_price'] = $eachRoomFoodPlanPrice; // * ($room['no_of_adult'] + $room['no_of_child'] / 2);
                    $orderRooms['bed_amount'] = $eachRoomBedAmount;
                    $orderRooms['early_check_in'] = $eachRoomEarlyCheckIn;
                    $orderRooms['late_check_out'] = $eachRoomLateCheckOut;
                    $orderRooms['extra_bed_qty'] = $room['extra_bed_qty'];

                    $orderRooms['price'] = $list['room_price']; //without tax
                    $orderRooms['total_with_tax'] = $list['price']; //with tax
                    $orderRooms['total'] = $list['total_price'];
                    $orderRooms['grand_total'] = $list['total_price'];

                    $orderRooms['single_day_extra_amount'] = $singleDayExtraAmount; //new
                    $orderRooms['single_day_discount'] = $singleDayDiscount; //new



                    //room price with regular calculation 
                    //divide room price with tax calculation
                    $total = ($list['total_price'] - $singleDayDiscount) + $singleDayExtraAmount;
                    $result = $this->divideTaxPrice($orderRooms['price'], $total, $bookedRoomId->company_id);
                    $room_price_without_tax = $result[0];
                    $room_tax = $result[1];
                    $orderRooms['price'] = $room_price_without_tax;
                    $orderRooms['cgst'] = $room_tax  / 2;
                    $orderRooms['sgst'] = $room_tax  / 2;
                    $orderRooms['room_tax'] = $room_tax;

                    $orderRooms['grand_total'] = $orderRooms['price'] + $orderRooms['room_tax'];

                    //recalculate price and miscellaneous and tax------------------------------------------------
                    $miscellaneous_total_with_tax =   $orderRooms['bed_amount']
                        + $orderRooms['food_plan_price']
                        + $orderRooms['early_check_in']
                        + $orderRooms['late_check_out']
                        + $orderRooms['single_day_extra_amount'];

                    $miscellaneous_without_extra_discount = $miscellaneous_total_with_tax - $orderRooms['single_day_discount'];

                    $orderRooms['base_price'] = $room_price_without_tax - $miscellaneous_without_extra_discount;



                    //with tax $list['price']; ------------------------------------------------
                    $room_price_with_tax = $list['price']  - $orderRooms['single_day_discount'];
                    $result = $this->divideTaxPrice($room_price_with_tax, $room_price_with_tax, $bookedRoomId->company_id);
                    $room_price_without_tax = $result[0];
                    $room_tax = $result[1];
                    $room_tax_percentage = $result[2];

                    $orderRooms['inv_room_listing_price'] = $room_price_without_tax;
                    $orderRooms['inv_room_cgst'] = round($room_tax  / 2, 2);
                    $orderRooms['inv_room_sgst'] = round($room_tax  / 2, 2);

                    $orderRooms['inv_room_tax_per'] = $room_tax_percentage;

                    Logger::channel("custom")->error("inv_room_tax_per: " . $room_tax_percentage);



                    //divide miscellaneous and tax-----------------------------------
                    $miscellaneous_total_without_tax = ($miscellaneous_total_with_tax * 100) / (100 + $company_food_tax);
                    $miscellaneous_tax = $miscellaneous_total_with_tax - $miscellaneous_total_without_tax;
                    $orderRooms['miscellaneous_total'] = $miscellaneous_total_with_tax; //new 
                    $orderRooms['miscellaneous_total_without_tax'] = $miscellaneous_total_without_tax; //new 
                    $orderRooms['miscellaneous_tax'] = $miscellaneous_tax; //new 

                    $orderRooms['inv_food_tax_per'] = $company_food_tax;




                    // recalculate 1-OLD without miscellaneous
                    /*
                    $total = ($list['total_price'] - $singleDayDiscount) + $singleDayExtraAmount;
                    $BookingObj = new BookingController();
                    $room_tax =   $BookingObj->getTaxSlab(($orderRooms['price']), $bookedRoomId->company_id);
                    $roomBasePrice = ($total * 100) / (100 + $room_tax);
                    $roomGSTAmount = $total - $roomBasePrice;
                    $orderRooms['price'] = $roomBasePrice;
                    $orderRooms['cgst'] = $roomGSTAmount / 2;
                    $orderRooms['sgst'] = $roomGSTAmount / 2;
                    $orderRooms['room_tax'] = $roomGSTAmount;

                    $room_tax_new =   $BookingObj->getTaxSlab(($roomBasePrice), $bookedRoomId->company_id);

                    if ($room_tax_new != $room_tax) {
                        $room_tax =   $BookingObj->getTaxSlab(($roomBasePrice), $bookedRoomId->company_id);
                        $roomBasePrice = ($total * 100) / (100 + $room_tax);
                        $roomGSTAmount = $total - $roomBasePrice;
                        $orderRooms['price'] = $roomBasePrice;
                        $orderRooms['cgst'] = $roomGSTAmount / 2;
                        $orderRooms['sgst'] = $roomGSTAmount / 2;
                        $orderRooms['room_tax'] = $roomGSTAmount;
                    }

                    $orderRooms['base_price'] =
                        $orderRooms['price']
                        - $orderRooms['bed_amount']
                        - $orderRooms['food_plan_price']
                        - $orderRooms['early_check_in']
                        - $orderRooms['late_check_out']
                        - $singleDayExtraAmount
                        + $singleDayDiscount;

                        */

                    OrderRoom::create($orderRooms);
                }
            }
        } catch (\Exception $e) {
            // Log::alert(json_encode($e->getMessage()));
            Logger::channel("custom")->error("New Booking Job: " . json_encode($e->getMessage()));

            // use Illuminate\Support\Facades\Log as Logger;

        }
    }
    public function  divideTaxPrice($slabtotal, $total, $company_id)
    {
        $BookingObj = new BookingController();
        $room_tax =   $BookingObj->getTaxSlab(($slabtotal), $company_id);
        $roomBasePrice = ($total * 100) / (100 + $room_tax);
        $roomGSTAmount = $total - $roomBasePrice;
        // $orderRooms['price'] = $roomBasePrice;
        // $orderRooms['cgst'] = $roomGSTAmount / 2;
        // $orderRooms['sgst'] = $roomGSTAmount / 2;
        // $orderRooms['room_tax'] = $roomGSTAmount;

        $room_tax_new =   $BookingObj->getTaxSlab(($roomBasePrice), $company_id);

        if ($room_tax_new != $room_tax) {
            $room_tax =   $BookingObj->getTaxSlab(($roomBasePrice), $company_id);
            $roomBasePrice = ($total * 100) / (100 + $room_tax);
            $roomGSTAmount = $total - $roomBasePrice;
            // $orderRooms['price'] = $roomBasePrice;
            // $orderRooms['cgst'] = $roomGSTAmount / 2;
            // $orderRooms['sgst'] = $roomGSTAmount / 2;
            // $orderRooms['room_tax'] = $roomGSTAmount;
        }

        return [$roomBasePrice, $roomGSTAmount, $room_tax];
    }
}
