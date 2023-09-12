<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\HallBookingExtraAmounts;
use App\Models\HallBookingFood;
use App\Models\HallBookings;
use App\Models\IdCardType;
use App\Models\RoomType;
use App\Models\Weekend;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class PartyHallController extends Controller
{
    public function index(Request $request)
    {

        $model =  HallBookings::where('company_id', $request->company_id)
            ->orderBy('created_at', 'desc');



        return $model->paginate($request->per_page ?? 30);
    }

    public function getBookingDetails(Request $request, $hallBookingId)
    {



        $model =  HallBookings::with(['customer', 'food', 'extraAmounts'])
            ->where('id', $hallBookingId);

        return  $model->get()->first();
    }

    public function getBookingUpcomingDetails(Request $request)
    {
        return  $this->getBookingsWithFilter($request, 'upcoming');
    }
    public function getBookingOnGoingDetails(Request $request)
    {
        return $this->getBookingsWithFilter($request, 'ongoing');
    }
    public function getBookingCompletedDetails(Request $request)
    {
        return  $this->getBookingsWithFilter($request, 'completed');
    }
    public function getBookingsWithFilter($request, $type)
    {
        $model =  HallBookings::with(['booking', 'event'])->where('company_id', $request->company_id);

        if ($type == 'upcoming') {
            $model->where('event_date', '>', date('Y-m-d'));
        } else if ($type == 'ongoing') {
            $model->where('event_date', '=', date('Y-m-d'));
        } else if ($type == 'completed') {
            $model->where('event_date', '<', date('Y-m-d'));
        }


        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('event_date', '>=', $request->from);
            $model->whereDate('event_date', '<=', $request->to);
        }

        $model->orderBy('event_date', 'desc');

        return $model->paginate($request->per_page ?? 30);
    }

    public function storeBookingInfo(Request $request)
    {



        // Create a new request instance and set the JSON data
        $hallBookingRequest = Request::create('hall-booking', 'POST', $request->Payload1);
        $bookingRequest = Request::create('/booking', 'POST', $request->Payload2);

        $data = (new BookingController)->store($bookingRequest);



        $booking_id = $data->original['data'];

        $this->storeBookingInfoHall($hallBookingRequest, $booking_id);
        return  $booking_id;
    }

    public function getHallPriceList(Request $request)
    {

        $company_id = $request->company_id;


        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price', 'projector_charges', 'cleaning_charges', 'electricity_charges', 'audio_charges']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->start, $request->start);
        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = (new BookingController)->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $prices->holiday_price,
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => (new BookingController)->getTaxSlab($prices->holiday_price, $company_id),
                    "room_price" => $prices->holiday_price,
                    "projector_charges" => $prices->projector_charges,
                    "cleaning_charges" => $prices->cleaning_charges,
                    "electricity_charges" => $prices->electricity_charges,
                    "audio_charges" => $prices->audio_charges,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $prices->weekend_price,
                    "tax" => (new BookingController)->getTaxSlab($prices->weekend_price, $company_id),
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                    "projector_charges" => $prices->projector_charges,
                    "cleaning_charges" => $prices->cleaning_charges,
                    "electricity_charges" => $prices->electricity_charges,
                    "audio_charges" => $prices->audio_charges,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $prices->weekday_price,
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" => (new BookingController)->getTaxSlab($prices->weekday_price, $company_id),
                    "room_price" => $prices->weekday_price,
                    "projector_charges" => $prices->projector_charges,
                    "cleaning_charges" => $prices->cleaning_charges,
                    "electricity_charges" => $prices->electricity_charges,
                    "audio_charges" => $prices->audio_charges,
                ];
            }
        }

        return  $arr;
    }
    public function storeBookingInfoHall($request, $booking_id)
    {

        $array = [];


        // $customer = [];
        // $customer['first_name'] = $request->partyHallBookingCustomer['first_name'];
        // $customer['last_name'] = $request->partyHallBookingCustomer['last_name'];
        // $customer['contact_no'] = $request->partyHallBookingCustomer['contact_no'];
        // $customer['email'] = $request->partyHallBookingCustomer['email'];
        // $customer['id_card_type_id'] = $request->partyHallBookingCustomer['id_card_type_id'];
        // $customer['id_card_no'] = $request->partyHallBookingCustomer['id_card_no'];
        // $customer['car_no'] = $request->partyHallBookingCustomer['car_no'];
        // $customer['no_of_adult'] = $request->partyHallBookingCustomer['no_of_adult'];
        // $customer['no_of_child'] = $request->partyHallBookingCustomer['no_of_child'];
        // $customer['no_of_baby'] = $request->partyHallBookingCustomer['no_of_baby'];
        // $customer['address'] = $request->partyHallBookingCustomer['address'];
        // $customer['company_id'] = $request->partyHallBookingCustomer['company_id'];
        // $customer['customer_type'] = $request->partyHallBookingCustomer['customer_type'];
        // $customer['dob'] = $request->partyHallBookingCustomer['dob'];
        // $customer['title'] = $request->partyHallBookingCustomer['title'];
        // $customer['whatsapp'] = $request->partyHallBookingCustomer['whatsapp'];
        // $customer['nationality'] = $request->partyHallBookingCustomer['nationality'];
        // $customer['gst_number'] = $request->partyHallBookingCustomer['gst_number'];

        // $customer_id = (new BookingController)->customerStore($customer);
        $array['customer_id'] = 0; //$customer_id;

        $array['room_id'] = $request->hallRoomId;
        $array['room_number'] = $request->hallRoomNumber;
        $array['reference_no'] = 0; //isset($request->partyHallBookingCustomer['reference_no'])  &&  null;
        $array['reservation_no'] = 0; //  (new BookingController)->getReservationNumber(["company_id" => $request->company_id]);;
        $array['company_id'] = $request->company_id;
        $array['user_id'] = $request->user_id;

        $array['booking_status'] = 0;
        $array['customer_request'] = 0; // isset($request->partyHallBookingCustomer['request'])  &&  null;
        $array['source_type'] = 0; //$request->partyHallBookingCustomer['source_type'];
        //if (isset($request->partyHallBookingCustomer['customer_type']))
        $array['customer_type'] = 0; // $request->partyHallBookingCustomer['customer_type'];

        $array['id_card_type'] = 0; // IdCardType::find($request->partyHallBookingCustomer['id_card_type_id'])->name ?? "";;
        $array['id_card_no'] = 0; //$request->partyHallBookingCustomer['id_card_no'];
        //if (isset($request->partyHallBookingCustomer['id_exp_date']))
        $array['id_exp_date'] = 0; //isset($request->partyHallBookingCustomer['id_exp_date'])  && null;
        $array['paid_by'] = 0; // isset($request->partyHallBookingCustomer['paid_by'])  && null;
        $array['purpose'] = 0; //isset($request->partyHallBookingCustomer['purpose'])  && null;



        $array['booking_date'] = date('Y-m-d');
        $array['booking_date_time'] = date('Y-m-d H:i:s');

        $array['event_date'] = $request->partyHallBookingEvents['date'];;
        $array['event_start_time'] = $request->partyHallBookingEvents['start_time'];;
        $array['event_end_time'] = $request->partyHallBookingEvents['end_time'];;
        $array['event_type_id'] = $request->partyHallBookingEvents['event_type'];;
        $array['event_pax'] = $request->partyHallBookingEvents['pax'];;
        $array['event_special_setup'] = $request->partyHallBookingEvents['special_setup'];;
        $array['event_audio_system'] = isset($request->partyHallBookingEvents['audio_system']) ? $request->partyHallBookingEvents['audio_system'] : 0;

        $array['event_projector'] = isset($request->partyHallBookingEvents['projector']) ? $request->partyHallBookingEvents['projector'] : 0;
        $array['event_stage_decoration'] = isset($request->partyHallBookingEvents['stage_decoration']) ? $request->partyHallBookingEvents['stage_decoration'] : 0;


        $hall_rent_amount_per_hour = $request->partyHallBookingAmount['hall_rent_amount_per_hour'];;;
        $hall_rent_amount = $request->partyHallBookingAmount['hallRentTotalAmount'];;
        $discount = $request->partyHallBookingAmount['discount'];;
        $hall_electricity_amount = $request->partyHallBookingAmount['electricityTotalAmount'];;
        $hall_projector_amount = $request->partyHallBookingAmount['projecterTotalAmount'];;
        $hall_audio_system = $request->partyHallBookingAmount['audioTotalAmount'];;
        $hall_cleaning_charges = $request->partyHallBookingAmount['cleaningTotalAmount'];;

        $array['hall_total_hours'] = $request->partyHallBookingEvents['end_time'];
        -$request->partyHallBookingEvents['start_time'];;
        $array['hall_rent_amount'] = $hall_rent_amount;
        $array['discount'] = $discount;

        $array['hall_rent_per_hour'] = $hall_rent_amount_per_hour;
        $array['hall_electricity_amount'] = $hall_electricity_amount;
        $array['hall_projector_amount'] = $hall_projector_amount;
        $array['hall_audio_system'] = $hall_audio_system;
        $array['hall_cleaning_charges'] = $hall_cleaning_charges;

        $BookingObj = new BookingController();
        //Hall + others
        $hall_total_amount_without_food = 0;


        $othersTotal = 0;
        //others 
        foreach ($request->partyHallBookingExtra['items'] as $extraitem) {

            if (isset($extraitem))
                $othersTotal += $extraitem['total'];
        }

        $hall_total_amount_without_food_without_tax = 0;
        $array['hall_extra_amounts_total'] = $othersTotal;

        //food 
        $foodTotal = 0;
        $testarray = [];
        foreach ($request->partyHallBookingFood['items'] as $foodItem) {

            $testarray[] = $foodItem;
            if (isset($foodItem))
                $foodTotal += $foodItem['total'];
        }


        $hall_total_amount_without_food = $request->partyHallBookingAmount['AmountGrandTotalWithFood'] - $foodTotal;


        $hall_tax_per = $BookingObj->getTaxSlab($hall_total_amount_without_food, $request->company_id);;
        $hall_tax_amount = 1;

        if ($hall_total_amount_without_food > 0 && $hall_tax_per) {
            $hall_total_amount_without_food_without_tax = ($hall_total_amount_without_food * 100) / (100 + $hall_tax_per);
            $hall_tax_amount = $hall_total_amount_without_food - $hall_total_amount_without_food_without_tax;
        }


        $food_tax_per = 5;
        $food_tax_amount = 0;
        $food_total_without_tax = 0;
        // if ($foodTotal > 0 && $food_tax_per)
        //     $food_tax_amount = ($foodTotal * $food_tax_per) / 100;
        if ($foodTotal > 0 && $food_tax_per) {
            $food_total_without_tax = ($foodTotal * 100) / (100 + $food_tax_per);
            $food_tax_amount = $foodTotal - $food_total_without_tax;
        }

        $array['hall_tax_per'] = $hall_tax_per;
        $array['hall_sgst_amount'] = $hall_tax_amount / 2;
        $array['hall_cgst_amount'] = $hall_tax_amount / 2;
        $array['food_total_amount'] = $foodTotal;
        $array['food_tax_per'] = $food_tax_per;

        $array['food_cgst_amount'] =  $food_tax_amount / 2;
        $array['food_sgst_amount'] = $food_tax_amount / 2;
        $array['inv_total_tax'] = $hall_tax_amount + $food_tax_amount;
        $array['inv_total_without_tax'] = $hall_total_amount_without_food_without_tax + $food_total_without_tax;
        $array['inv_total'] = $array['inv_total_without_tax'] + $array['inv_total_tax'];
        $array['booking_id'] = $booking_id;



        $hallBookingObj =  HallBookings::create($array);

        $hallBookingId = $hallBookingObj->id;

        //partyHallBookingExtra
        foreach ($request->partyHallBookingExtra['items'] as $extraitem) {
            if ($extraitem['total'] > 0) {
                $extraitem['booking_id'] = $hallBookingId;
                $hallBookingObj =  HallBookingExtraAmounts::create($extraitem);
            }
        }
        //food 
        foreach ($request->partyHallBookingFood['items'] as $extraitem) {
            if (isset($extraitem))
                if ($extraitem['total'] > 0) {
                    $extraitem['booking_id'] = $hallBookingId;
                    $hallBookingObj =  HallBookingFood::create($extraitem);
                }
        }

        return $array;
    }
}
