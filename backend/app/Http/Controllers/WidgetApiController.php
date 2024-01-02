<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TaxSlabs;
use App\Models\Weekend;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class WidgetApiController extends Controller
{
    public function getAvailableRoomList(Request $request)
    {
        $model = BookedRoom::query();
        $bookedRoomIds = $model
            ->whereDate('check_in', '<=', $request->from_date)
            ->WhereDate('check_out', '>=', $request->to_date)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
            })
            ->pluck('room_id');


        // $bookedRoomIds = BookedRoom::withOut('booking', 'postings')
        //     ->where('company_id', $request->company_id)
        //     ->whereHas('booking', function ($q) use ($request) {
        //         $q->where('booking_status', '!=', 0);
        //         $q->where('company_id', $request->company_id);
        //     })
        //     // ->where('booking_status', '<=', 2)
        //     ->where(function ($query) use ($request) {
        //         $query->where(function ($query) use ($request) {
        //             $query->where('check_in', '>=', $request->from_date . ' 00:00:00')
        //                 ->where('check_in', '<=', $request->from_date . ' 23:59:59');
        //         });
        //         $query->orWhere(function ($query) use ($request) {
        //             $query->where('check_out', '<=', $request->from_date . ' 00:00:00')
        //                 ->where('check_out', '>=', $request->from_date . ' 23:59:59');
        //         });
        //     })
        //     ->pluck('room_id');

        $unbookedRoomsInfo = Room::whereNotIn('id', $bookedRoomIds)
            ->where('company_id', $request->company_id)
            ->get();

        $groupedRooms = [];
        foreach ($unbookedRoomsInfo as $roomInfo) {

            $roomType = $roomInfo['room_type']['name'];

            if ($groupedRooms && !isset($groupedRooms[$roomType])) {
                $groupedRooms[$roomType] = [];
            }

            $groupedRooms[$roomType][] = $roomInfo;
            //$groupedRooms[$roomType][] = $this->getRoomPrice($request, $roomInfo);

        }
        return ["data" => $groupedRooms];
    }
    public function getAvailableRoomList_old(Request $request)
    {


        $rooms = Room::with('roomType')->where('company_id', $request->company_id)->get()->toArray();

        $bookedDates = BookedRoom::select('id', 'booking_id', 'room_no', 'room_type')->withOut('booking', 'postings')
            ->where('company_id', $request->company_id)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
            })
            // ->where('booking_status', '<=', 2)
            ->where(function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('check_in', '>=', $request->from_date . ' 00:00:00')
                        ->where('check_in', '<=', $request->from_date . ' 23:59:59');
                });
                $query->orWhere(function ($query) use ($request) {
                    $query->where('check_out', '<=', $request->from_date . ' 00:00:00')
                        ->where('check_out', '>=', $request->from_date . ' 23:59:59');
                });
            })
            //->where('check_in', '>=', $request->from_date . ' 00:00:00')
            ->orderBy('room_no', 'ASC')->get()->toArray();

        $bookedRoomNumbers = array_column($bookedDates, 'room_no');

        $allRoomNumbers = array_column($rooms, 'room_no');

        $unbookedRoomNumbers = array_diff($allRoomNumbers, $bookedRoomNumbers);

        $unbookedRoomsInfo = [];
        foreach ($rooms as $room) {
            if (in_array($room['room_no'], $unbookedRoomNumbers)) {
                $unbookedRoomsInfo[] = $room;
            }
        }

        usort($unbookedRoomsInfo, function ($room1, $room2) {
            $price1 = floatval($room1['price']);
            $price2 = floatval($room2['price']);

            if ($price1 == $price2) {
                return 0;
            }
            return ($price1 > $price2) ? -1 : 1; // Change comparison to sort in descending order
        });

        // usort($unbookedRoomsInfo, function ($a, $b) {
        //     return strcmp($a['price'], $b['price']);
        // });

        // Group unbooked room info by room type
        $groupedRooms = [];
        foreach ($unbookedRoomsInfo as $roomInfo) {

            $roomType = $roomInfo['room_type']['name'];

            if ($groupedRooms && !isset($groupedRooms[$roomType])) {
                $groupedRooms[$roomType] = [];
            }

            $groupedRooms[$roomType][] = $roomInfo;
            //$groupedRooms[$roomType][] = $this->getRoomPrice($request, $roomInfo);

        }
        return ["data" => $groupedRooms];
    }
    public function compareByPrice($room1, $room2)
    {
        $price1 = floatval($room1['price']);
        $price2 = floatval($room2['price']);

        if ($price1 == $price2) {
            return 0;
        }
        return ($price1 < $price2) ? -1 : 1;
    }
    public function checkOutDate($date)
    {
        $date = date_create($date);
        date_modify($date, "-1 days");
        return date_format($date, "Y-m-d");
    }

    public function checkHoliday($date, $company_id)
    {
        return Holiday::where(function ($q) use ($date) {
            $q->where('from', '<=', $date);
            $q->where('to', '>=', $date);
        })->whereCompanyId($company_id)->exists();
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
        // $per = $amount < 2500 ? 12 : 18;
        // if ($amount > 7500) {
        //     $per = 28;
        // }

        $per = $this->getTaxSlab($amount, $company_id);

        $tax = ($amount / 100) * $per;
        $temp['room_tax'] = $tax;
        $temp['total_with_tax'] = (float) $amount + (float) $tax;
        $temp['after_discount'] = $amount;
        $gst = floatval($tax) / 2;
        $temp['cgst'] = $gst;
        $temp['sgst'] = $gst;
        return $temp;
    }
    public function getRoomPrice($request, $newRoom)
    {

        $company_id = $request->company_id;
        $discount = 0;
        $room = Room::where('room_no', $newRoom['room_no'])->where('company_id', $request->company_id)->first();
        $prices = RoomType::whereCompanyId($request->company_id)->whereName($newRoom['room_type']['name'])
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->from_date, $this->checkOutDate($request->to_date));

        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->holiday_price - $discount, $request->company_id)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->holiday_price - $discount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekend_price - $discount, $request->company_id)['total_with_tax'],
                    "tax" => $this->getRoomTax($prices->weekend_price - $discount, $request->company_id)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekday_price - $discount, $request->company_id)['total_with_tax'],
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->weekday_price - $discount, $request->company_id)['room_tax'],
                    "room_price" => $prices->weekday_price,
                ];
            }
        }

        return [
            'room' => $room,
            'price_list' => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
            'total_tax' => array_sum(array_column($arr, "tax")),
        ];
    }

    public function test()
    {
    }
}
