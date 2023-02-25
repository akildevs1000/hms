<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Room;
use App\Models\RoomType;
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
        return RoomType::get(['id', 'name', 'price']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getPriceList(Request $request)
    {
        return RoomType::where('company_id', $request->company_id)->get();
    }

    public function getDataBySelect(Request $request)
    {
        $company_id = $request->company_id;
        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekends = ["Sat", "Sun"];

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
                $arr[] = ["date" => $iteration_date, "price"  => $prices->weekday_price, "day_type" => "weekday", "day" => $day];
            }
        }

        // return [
        //     'data' => $arr,
        //     'total_price' => array_sum(array_column($arr, "price")),
        // ];


        return Room::where('room_no', $request->room_no)
            ->where('company_id', $request->company_id)
            ->first();
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
}
