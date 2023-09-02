<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\OrderRoom;
use App\Models\Posting;

class RecalculateTaxController extends Controller
{
    public function UpdateTax()
    {

        $id = 2877;
        // $this->UpdateTaxWithID($id);

        // return;
        $bookings = Booking::with(['orderRooms'])->where('tax_recalculated_status', 0)
            ->where('booking_status', '!=', -1)
            ->orderBy('check_in', 'DESC')
            ->limit(500)->get();



        foreach ($bookings as   $booking) {
            echo '  ' . $booking->id . '--'  .  $this->UpdateTaxWithID($booking->id);
        }

    }

    public function exportCsv($tasks)
    {
        $fileName = 'tasks.csv';
        //$tasks = Task::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Title', 'Assign', 'Description', 'Start Date', 'Due Date');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $rowData = [
                    $task->title,
                    $task->title,

                ];

                fputcsv($file, $rowData);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function UpdateTaxWithID($id)
    {


        $booking = Booking::with(['orderRooms'])->find($id);

        $orderRooms = $booking->orderRooms;


        $totalFoodGst = 0;

        $inv_total_tax_collected = 0;
        $inv_total_without_tax_collected = 0;
        //include food and room price in single bill(room)

        $merge_food_in_room_price = $booking->merge_food_in_room_price;

        foreach ($orderRooms as $room) {

            //include food and room price in single bill(room)
            if ($merge_food_in_room_price) {
                $totalFoodCost = $room->tot_adult_food + $room->tot_child_food;


                $food_tax = 0;
                $foodBasePrice = 0;
                $foodGstAmount = 0;

                $totalFoodGst += $foodGstAmount;

                //recalculating room base price and GST

                $roomTotal = $room->total;

                $BookingObj = new BookingController();
                $room_tax =   $BookingObj->getTaxSlab(($roomTotal + 900), $booking->company_id);


                $roomBasePrice = ($roomTotal * 100) / (100 + $room_tax);
                $roomGSTAmount = $roomTotal - $roomBasePrice;

                $room_discount = $room->disocunt && 0;

                $roomExtraColumns = [];
                $roomExtraColumns['inv_room_listing_price'] = $roomBasePrice + $room->disocunt;
                $roomExtraColumns['inv_room_discount'] = $room_discount;
                $roomExtraColumns['inv_room_price_after_discount'] = $roomBasePrice;

                $roomExtraColumns['inv_room_cgst'] = $roomGSTAmount / 2;
                $roomExtraColumns['inv_room_sgst'] = $roomGSTAmount / 2;

                $roomExtraColumns['inv_room_tax_per'] = $room_tax;

                $roomExtraColumns['inv_room_price_with_tax'] = $roomBasePrice + $roomGSTAmount;
                //-------------FOOD-----
                $roomExtraColumns['inv_food_listing_price'] = 0;
                $roomExtraColumns['inv_food_sgst'] = 0;
                $roomExtraColumns['inv_food_cgst'] = 0;

                $roomExtraColumns['inv_food_tax_per'] = 0;

                $roomExtraColumns['inv_food_price_with_tax'] = 0;

                $roomExtraColumns['inv_room_food_gst_grand_total'] = $roomExtraColumns['inv_room_price_with_tax'] + $roomExtraColumns['inv_food_price_with_tax'];

                OrderRoom::whereId($room->id)->update($roomExtraColumns);


                $inv_total_tax_collected += ($foodGstAmount + $roomGSTAmount);
                $inv_total_without_tax_collected += ($foodBasePrice + $roomBasePrice);
            } else {
                //food and room seperate in bill 
                $totalFoodCost = $room->tot_adult_food + $room->tot_child_food;


                $food_tax = 5;
                $foodBasePrice = ($totalFoodCost * 100) / (100 + $food_tax);
                $foodGstAmount = $totalFoodCost - $foodBasePrice;

                $totalFoodGst += $foodGstAmount;

                //recalculating room base price and GST

                $roomTotal = $room->total - ($room->tot_adult_food + (float) $room->tot_child_food);
                $BookingObj = new BookingController();
                $room_tax =   $BookingObj->getTaxSlab($roomTotal + 900, $booking->company_id);


                $roomBasePrice = ($roomTotal * 100) / (100 + $room_tax);
                $roomGSTAmount = $roomTotal - $roomBasePrice;

                $room_discount = $room->disocunt && 0;

                $roomExtraColumns = [];
                $roomExtraColumns['inv_room_listing_price'] = $roomBasePrice + $room->disocunt;
                $roomExtraColumns['inv_room_discount'] = $room_discount;
                $roomExtraColumns['inv_room_price_after_discount'] = $roomBasePrice;

                $roomExtraColumns['inv_room_cgst'] = $roomGSTAmount / 2;
                $roomExtraColumns['inv_room_sgst'] = $roomGSTAmount / 2;

                $roomExtraColumns['inv_room_tax_per'] = $room_tax;

                $roomExtraColumns['inv_room_price_with_tax'] = $roomBasePrice + $roomGSTAmount;
                //-------------FOOD-----
                $roomExtraColumns['inv_food_listing_price'] = $foodBasePrice;
                $roomExtraColumns['inv_food_sgst'] = $foodGstAmount / 2;
                $roomExtraColumns['inv_food_cgst'] = $foodGstAmount / 2;

                $roomExtraColumns['inv_food_tax_per'] = $food_tax;

                $roomExtraColumns['inv_food_price_with_tax'] = $foodBasePrice + $foodGstAmount;

                $roomExtraColumns['inv_room_food_gst_grand_total'] = $roomExtraColumns['inv_room_price_with_tax'] + $roomExtraColumns['inv_food_price_with_tax'];

                OrderRoom::whereId($room->id)->update($roomExtraColumns);


                $inv_total_tax_collected += ($foodGstAmount + $roomGSTAmount);
                $inv_total_without_tax_collected += ($foodBasePrice + $roomBasePrice);
            }
        }
        $postings = Posting::where('booking_id', $id)->get();

        foreach ($postings as $post) {


            $inv_total_tax_collected += ($post->cgst + $post->sgst);
            $inv_total_without_tax_collected += $post->amount;
        }



        $data = [];
        $data['tax_recalculated_status'] = 1;
        $data['inv_total_without_tax_collected'] = $inv_total_without_tax_collected;
        $data['inv_total_tax_collected'] = $inv_total_tax_collected;
        $status = Booking::whereId($id)->update($data);

        //echo ('Updated Booking id ' . $id . '-' . $booking->check_in . '<br/>');
    }
}
