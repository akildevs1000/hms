<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingRequest;
use App\Http\Requests\Booking\DocumentRequest;
use App\Jobs\StoreBookedRoomsJob;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Customer;
use App\Models\Food;
use App\Models\HallBookings;
use App\Models\Holiday;
use App\Models\IdCardType;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\PostingPayment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Source;
use App\Models\SubCustomer;
use App\Models\SubCustomerRoomHistory;
use App\Models\TaxSlabs;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\Weekend;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Storage;

class InvoiceRecalWithoutFoodController   extends Controller
{
    public function test(Request $request)
    {





        //SELECT * FROM order_rooms WHERE booking_id=372
        // SELECT * FROM bookings WHERE id=358  


        $id = 373;
        if ($request->filled("id")) {
            $id = $request->id;
        }
        // return;
        $bookings = Booking::with(['orderRooms'])->where('tax_recalculated_status', 0)
            ->where('booking_status', '!=', -1)
            ->where('id', $id)



            ->orderBy('check_in', 'DESC')
            ->limit(1)->get();



        foreach ($bookings as   $booking) {
            return [$booking->id, $this->UpdateTaxWithID($booking->id)];
        }

        if (count($bookings) == 0) return "No Booking is exist " . $id;
    }

    public function UpdateTaxWithID($id)
    {


        $booking = Booking::with(['orderRooms'])->find($id);

        $orderRooms = $booking->orderRooms;

        foreach ($orderRooms as $room) {


            $price = '';
            $sgst = '';
            $cgst = '';
            // $total_with_tax =   $room->grand_total; //+ $room->early_check_in; //3000

            // if (count($orderRooms) > 1)
            //     $total_with_tax =   $room->grand_total + $room->early_check_in - ($room->food_plan_price / count($orderRooms)); //3000
            // // return [$total_with_tax, $room->grand_total, $room->early_check_in, ($room->food_plan_price / count($orderRooms))];

            //$total_with_tax = $room->grand_total;
            $total_with_tax = $room->base_price + $room->food_plan_price + $room->early_check_in + $room->late_check_out + (($booking->total_extra - $booking->discount) / count($orderRooms));

            $BookingObj = new BookingController();
            $room_tax =   $BookingObj->getTaxSlab(($total_with_tax), $room->company_id);


            $roomBasePrice = ($total_with_tax * 100) / (100 + $room_tax);
            $roomGSTAmount = $total_with_tax - $roomBasePrice;

            $data = [
                "price" => round($roomBasePrice, 2),
                "sgst" => round($roomGSTAmount / 2, 2),
                "cgst" => round($roomGSTAmount / 2, 2),
                "total_with_tax" => $total_with_tax,
                "room_tax" => $roomGSTAmount,


            ];
            OrderRoom::where("id", $room->id)->update($data);
        }

        Booking::where("id", $id)->update(["tax_recalculated_status" => 1]);


        //echo ('Updated Booking id ' . $id . '-' . $booking->check_in . '<br/>');
    }
}
