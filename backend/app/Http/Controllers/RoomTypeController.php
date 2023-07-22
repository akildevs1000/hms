<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Holiday;
use App\Models\Weekend;
use App\Models\RoomType;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Requests\RoomType\StoreRequest;
use App\Http\Requests\RoomType\UpdateRequest;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.php
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return RoomType::whereCompanyId($request->company_id)->get(['id', 'name', 'price']);
    }

    public function getRoomType(Request $request)
    {
        $model =  RoomType::whereCompanyId($request->company_id);

        if ($request->filled('search') && $request->search) {
            $model->where('name', 'ILIKE', "%$request->search%");
        }

        return $model->paginate($request->per_page ?? 50);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['holiday_price'] = $data['weekday_price'] = $data['weekend_price'] = $data['price'];

        // return $data;

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

    public function update(UpdateRequest $request, RoomType $roomType)
    {
        try {
            $data = $request->validated();
            $data['holiday_price'] = $data['weekday_price'] = $data['weekend_price'] = $data['price'];
            $record = $roomType->update($data);
            if ($record) {
                return $this->response('Room Category successfully updated.', $record, true);
            } else {
                return $this->response('Room Category cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getPriceList(Request $request)
    {
        return RoomType::where('company_id', $request->company_id)->get();
    }

    public function getDataBySelect(Request $request)
    {
        $company_id = $request->company_id;
        $discount   = $request->discount ?? 0;
        $room       = Room::where('room_no', $request->room_no)->where('company_id', $request->company_id)->first();
        $prices     = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel =  Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr    = [];
        $period = CarbonPeriod::create($request->checkin, $this->checkOutDate($request->checkout));
        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day            = $date->format('D');
            $isWeekend      = in_array($day, $weekends);
            $isHoliday      = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = ["date" => $iteration_date, "price" => $prices->holiday_price, "day_type" => "holiday", "day" => $day];
            } elseif ($isWeekend) {
                $arr[] = ["date" => $iteration_date, "price" => $prices->weekend_price, "day_type" => "weekend", "day" => $day];
            } else {
                $arr[] = ["date" => $iteration_date, "price" => $prices->weekday_price, "day_type" => "weekday", "day" => $day];
            }
        }

        return [
            'room'        => $room,
            'data'        => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    public function getDataBySelectWithTax(Request $request)
    {
        // return app()->isProduction();
        $company_id = $request->company_id;
        $discount   = $request->discount ?? 0;
        $room       = Room::where('room_no', $request->room_no)->where('company_id', $request->company_id)->first();
        $prices     = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel =  Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr    = [];
        $period = CarbonPeriod::create($request->checkin, $this->checkOutDate($request->checkout));
        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day            = $date->format('D');
            $isWeekend      = in_array($day, $weekends);
            $isHoliday      = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->holiday_price - $discount)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" =>  $this->getRoomTax($prices->holiday_price - $discount)['room_tax'],
                    "room_price" =>  $prices->holiday_price,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekend_price - $discount)['total_with_tax'],
                    "tax" =>  $this->getRoomTax($prices->weekend_price - $discount)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" =>  $prices->weekend_price,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekday_price - $discount)['total_with_tax'],
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" =>  $this->getRoomTax($prices->weekday_price - $discount)['room_tax'],
                    "room_price" =>  $prices->weekday_price,
                ];
            }
        }

        return [
            'room'        => $room,
            'data'        => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
            'total_tax' => array_sum(array_column($arr, "tax")),
        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    private function getRoomTax($amount)
    {
        $temp             = [];
        $per              = $amount < 3000 ? 12 : 18;
        $tax              = ($amount / 100) * $per;
        $temp['room_tax'] = $tax;

        // return (float) $amount + (float) $tax;

        $temp['total_with_tax'] = (float) $amount + (float) $tax;
        $temp['after_discount'] = $amount;
        $gst                    = floatval($tax) / 2;
        $temp['cgst']           = $gst;
        $temp['sgst']           = $gst;
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
        try {
            $model = RoomType::find($id);
            $model->update($request->all());
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



}
