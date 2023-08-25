<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\OrderRoom;
use App\Models\Posting;

class RecalculateTaxController extends Controller
{

    public function UpdateTax()
    {

        // $id = 3056;
        // $this->UpdateTaxWithID($id);

        // return;
        $bookings = Booking::with(['orderRooms'])->where('tax_recalculated_status', 0)
            ->where('booking_status', '!=', -1)
            ->orderBy('check_in', 'DESC')
            ->limit(1)->get();




        foreach ($bookings as   $booking) {
            $this->UpdateTaxWithID(3180);
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




        // $numberOfCustomers = $booking->bookedRooms->sum(function ($room) {
        //     return $room->no_of_adult + $room->no_of_child + $room->no_of_baby;
        // });

        // $roomsDiscount = $booking->bookedRooms->sum(function ($room) {
        //     return $room->room_discount;
        // });



        $totalFoodGst = 0;

        $inv_total_tax_collected = 0;
        $inv_total_without_tax_collected = 0;

        foreach ($orderRooms as $room) {
            $totalFoodCost = $room->tot_adult_food + $room->tot_child_food;

            $food_tax = 5;
            $foodBasePrice = ($totalFoodCost * 100) / (100 + $food_tax);
            $foodGstAmount = $totalFoodCost - $foodBasePrice;

            $totalFoodGst += $foodGstAmount;

            //recalculating room base price and GST

            $roomTotal = $room->total - ($room->tot_adult_food + (float) $room->tot_child_food);

            $room_tax = 12;
            if ($roomTotal > 8850) {
                $room_tax = 18;
            }

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
