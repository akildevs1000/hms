<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomType\StoreRequest;
use App\Http\Requests\RoomType\UpdateRequest;
use App\Models\AdditionalCharge;
use App\Models\BookedRoom;
use App\Models\FoodPlan;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TaxSlabs;
use App\Models\Weekend;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.php
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return RoomType::whereCompanyId($request->company_id)->where("type", request("type", "room"))->get();
    }

    public function roomTypeForHall(Request $request)
    {
        return RoomType::whereCompanyId($request->company_id)
            // ->whereDoesntHave("booking")
            ->where("type", request("type", "hall"))->get();
    }

    public function getRoomType(Request $request)
    {
        $model = RoomType::whereCompanyId($request->company_id)->where("type", request("type", "room"));

        if ($request->filled('search') && $request->search) {
            $model->where('name', env("WILD_CARD") ?? 'ILIKE', "%$request->search%");
        }
        $model->orderBy('name', 'asc');
        return $model->paginate(1000);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['holiday_price'] = $data['weekday_price'] = $data['price'];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path = $file->storeAs('public/rooms/', $fileName);

            $data["pic"] = $fileName ?? "";
        }

        try {
            $record = RoomType::create($data);
            if ($record) {
                return $this->response('Room Category Successfully created.', $record, true);
            } else {
                // return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request, $id)
    {

        try {

            $data = $request->validated();

            $data['holiday_price'] = $data['weekday_price'] = $data['weekend_price'] = $data['price'];

            if ($request->hasFile('image')) {

                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $path = $file->storeAs('public/rooms', $fileName);

                $data["pic"] = $fileName ?? "";
            };

            $record = RoomType::where("id", $id)->update($data);
            if ($record) {
                return $this->response('Room Category successfully updated.', RoomType::where("id", $id)->first(), true);
            } else {
                return $this->response('Room Category cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getPriceList(Request $request)
    {
        return RoomType::where('company_id', $request->company_id)->where("type", request("type", "room"))->get();
    }

    public function getDataBySelect(Request $request)
    {
        $company_id = $request->company_id;
        $discount = $request->discount ?? 0;
        $room = Room::where('room_no', $request->room_no)->where('company_id', $request->company_id)->first();
        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->checkin, $this->checkOutDate($request->checkout));
        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = ["date" => $iteration_date, "price" => $prices->holiday_price, "day_type" => "holiday", "day" => $day];
            } elseif ($isWeekend) {
                $arr[] = ["date" => $iteration_date, "price" => $prices->weekend_price, "day_type" => "weekend", "day" => $day];
            } else {
                $arr[] = ["date" => $iteration_date, "price" => $prices->weekday_price, "day_type" => "weekday", "day" => $day];
            }
        }

        return [
            'room' => $room,
            'data' => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    public function getDataBySelectWithTax(Request $request)
    {

        $diff_in_seconds = strtotime($request->checkin) - strtotime(date('Y-m-d'));
        if ($diff_in_seconds < 0) {
            return response()->json(['data' => 'Booking Date is invalid', 'status' => false]);
        }

        // return app()->isProduction();
        $company_id = $request->company_id;
        $discount = $request->discount ?? 0;
        $eachRoomDiscount = $discount;
        $room = Room::where('room_no', $request->room_no)->where('company_id', $request->company_id)->first();

        $foodplan = 0;

        if ($request->BookedRoomId) {
            $BookedRoom = BookedRoom::where("room_id", $request->BookedRoomId)->with("foodplan")->first();
            if ($BookedRoom) {
                $foodplan = $BookedRoom->foodplan->unit_price;
            }
        }

        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->checkin, $this->checkOutDate($request->checkout));

        $eachRoomDiscount = $discount > 0 ? $discount / count($period) : 0;

        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax(($prices->holiday_price + $foodplan) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->holiday_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                    "discount" => $eachRoomDiscount,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax(($prices->weekend_price + $foodplan) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "tax" => $this->getRoomTax($prices->weekend_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                    "discount" => $eachRoomDiscount,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax(($prices->weekday_price + $foodplan) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->weekday_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->weekday_price,
                    "discount" => $eachRoomDiscount,
                ];
            }
        }

        return [
            'room' => $room,
            'data' => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
            'total_tax' => array_sum(array_column($arr, "tax")),

            'total_price_after_discount' => array_sum(array_column($arr, "price")),
            'total_discount' => array_sum(array_column($arr, "discount")),
            'status' => true,

        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    public function get_hall_pricing_list(Request $request)
    {

        $diff_in_seconds = strtotime($request->checkin) - strtotime(date('Y-m-d'));
        if ($diff_in_seconds < 0) {
            return response()->json(['data' => 'Booking Date is invalid', 'status' => false]);
        }

        // return app()->isProduction();
        $company_id = $request->company_id;
        $discount = $request->discount ?? 0;
        $eachRoomDiscount = $discount;
        $room = Room::where('room_no', $request->room_no)->where('company_id', $request->company_id)->first();

        $foodplan = 0;

        if ($request->BookedRoomId) {
            $BookedRoom = BookedRoom::where("room_id", $request->BookedRoomId)->with("foodplan")->first();
            if ($BookedRoom) {
                $foodplan = $BookedRoom->foodplan->unit_price;
            }
        }

        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first([
                'holiday_price',
                'weekend_price',
                'weekday_price',
                'hall_min_hours',
                'cleaning_charges',

                'electricity_charges',
                'audio_charges',
                'projector_charges',
                'generator_charges',
                'extra_hours_charges',

            ]);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->checkin, $request->checkout);

        $eachRoomDiscount = $discount > 0 ? $discount / count($period) : 0;

        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax(($prices->holiday_price + $foodplan) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->holiday_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                    "discount" => $eachRoomDiscount,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax(($prices->weekend_price + $foodplan) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "tax" => $this->getRoomTax($prices->weekend_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                    "discount" => $eachRoomDiscount,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax(($prices->weekday_price + $foodplan) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->weekday_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->weekday_price,
                    "discount" => $eachRoomDiscount,
                ];
            }
        }

        return [
            'room_type_data' => $prices ?? false,
            'room' => $room,
            'data' => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
            'total_tax' => array_sum(array_column($arr, "tax")),

            'total_price_after_discount' => array_sum(array_column($arr, "price")),
            'total_discount' => array_sum(array_column($arr, "discount")),
            'status' => true,

        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }


    public function getTaxSlab($amount, $company_id)
    {

        $tax = env('GST_TAX_DEFAULT');
        $TaxSlab = TaxSlabs::where('company_id', $company_id)
            ->where('start_price', '<=', $amount)->where('end_price', '>=', $amount)->pluck('tax');

        if (isset($TaxSlab[0])) {
            $tax = $TaxSlab[0];
        }

        return  $tax;
    }
    private function getRoomTax($amount, $company_id)
    {
        $temp = [];
        // $per = $amount < 2499 ? 12 : 18;
        // if ($amount > 7499) {
        //     $per = 28;
        // }
        $per = $this->getTaxSlab($amount, $company_id);
        $tax = ($amount / 100) * $per;
        $temp['room_tax'] = $tax;
        $temp['tax_percentage'] = $per;

        // return (float) $amount + (float) $tax;

        $temp['total_with_tax'] = (float) $amount + (float) $tax;
        $temp['after_discount'] = $amount;
        $gst = floatval($tax) / 2;
        $temp['cgst'] = $gst;
        $temp['sgst'] = $gst;
        return $temp;
    }

    public function checkHoliday($date, $company_id)
    {
        return Holiday::where(function ($q) use ($date) {
            $q->where('from', '<=', $date);
            $q->where('to', '>=', $date);
        })->whereCompanyId($company_id)->exists();
    }

    public function getDataBySelect_old(Request $request)
    {
        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    public function updatePrice(Request $request, $id)
    {
        //$wordpress_push_prices_url = Company::where("id", $request->company_id)->pluck('wordpress_push_prices_url')->first();
        try {
            RoomType::where("id", $id)->update($request->all());

            return $this->response('Successfully update', null, true);
        } catch (\Throwable $th) {
            return $this->response($th . ' Unsuccessfully update.', null, false);
        }
    }

    public function checkOutDate($date)
    {
        $date = date_create($date);
        date_modify($date, "-1 days");
        return date_format($date, "Y-m-d");
    }

    public function getRoomCurrentPrice(Request $request)
    {
        $foodplan = FoodPlan::find($request->food_plan_id);

        $diff_in_seconds = strtotime($request->checkin) - strtotime(date('Y-m-d'));
        if ($diff_in_seconds < 0) {
            return response()->json(['data' => 'Booking Date is invalid', 'status' => false]);
        }

        // return app()->isProduction();
        $company_id = $request->company_id;
        $discount = $request->discount ?? 0;
        $eachRoomDiscount = $discount;
        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->checkin, $this->checkOutDate($request->checkout));

        $eachRoomDiscount = $discount > 0 ? $discount / count($period) : 0;

        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "foodplan_price" => $foodplan->unit_price,
                    "price" => $this->getRoomTax((($foodplan->unit_price ?? 0) + $prices->holiday_price) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => $this->getRoomTax((($foodplan->unit_price ?? 0) + $prices->holiday_price) - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                    "discount" => $eachRoomDiscount,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "foodplan_price" => $foodplan->unit_price,
                    "price" => $this->getRoomTax((($foodplan->unit_price ?? 0) + $prices->weekend_price) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "tax" => $this->getRoomTax((($foodplan->unit_price ?? 0) + $prices->weekend_price) - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                    "discount" => $eachRoomDiscount,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "foodplan_price" => $foodplan->unit_price,
                    "price" => $this->getRoomTax((($foodplan->unit_price ?? 0) + $prices->weekday_price) - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" => $this->getRoomTax((($foodplan->unit_price ?? 0) + $prices->weekday_price) - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->weekday_price,
                    "discount" => $eachRoomDiscount,
                ];
            }
        }
        $totalCharge = 0;

        $AdditionalCharge = AdditionalCharge::where('company_id', request('company_id', 0))->first();


        if ($AdditionalCharge) {
            if ($request->is_early_check_in) {
                $totalCharge += $AdditionalCharge->early_check_in;
            }
            if ($request->is_late_check_out) {
                $totalCharge += $AdditionalCharge->late_check_out;
            }
        }

        $totalPrice = array_sum(array_column($arr, "price"));
        $totalTax = array_sum(array_column($arr, "tax"));

        $roomCount = count($request->room_ids ?? []);

        $newCharge = ($totalPrice) * $roomCount;

        // $newCharge = $newCharge + $totalCharge;

        $roomPrice = ($arr[0]["room_price"] ?? 0) + $foodplan->unit_price;


        return [
            'date' => $request->checkin,
            'day' => $arr[0]["day"] ?? date("D"),
            'day_type' => $arr[0]["day_type"] ?? date("D"),
            'room_type' => $request->roomType,
            'tariff' => $arr[0]["room_price"] ?? 0,
            'adult_per_room' => (int) $request->adult_per_room,
            'child_per_room' => (int) $request->child_per_room,
            'foodplan' => $foodplan->title,
            'foodplan_price' => $foodplan->unit_price,
            'no_of_room' => $roomCount,
            'room_price' => $roomPrice,
            'room_group_total' => $newCharge,
            'total_price' => $newCharge,
            'total_tax' => $totalTax,
            'total_price_after_discount' => $totalPrice,
            'total_discount' => array_sum(array_column($arr, "discount")),
            'status' => true,

            "early_check_in" => $AdditionalCharge->early_check_in ?? 0,
            "late_check_out" => $AdditionalCharge->late_check_out ?? 0,

            "is_early_check_in" => $request->is_early_check_in ? 1 : 0,
            "is_late_check_out" => $request->is_late_check_out ? 1 : 0,


            'check_in_display' => $request->check_in_display,
            'check_out_display' => $request->check_out_display,

            'formattedCheckinDate' => $request->formattedCheckinDate,
            'formattedCheckOutDate' => $request->formattedCheckOutDate,

            'room_price_list' => $arr,
        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    public function destroy(RoomType $RoomType)
    {
        $RoomType->delete();

        return response()->noContent();
    }
}
