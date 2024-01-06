<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomType\StoreRequest;
use App\Http\Requests\RoomType\UpdateRequest;
use App\Models\Company;
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
        return RoomType::whereCompanyId($request->company_id)->get(['id', 'name', 'price']);
    }

    public function getRoomType(Request $request)
    {
        $model = RoomType::whereCompanyId($request->company_id);

        if ($request->filled('search') && $request->search) {
            $model->where('name', 'ILIKE', "%$request->search%");
        }
        $model->orderBy('name', 'asc');
        return $model->paginate($request->per_page ?? 50);
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

    public function update(UpdateRequest $request, RoomType $roomType)
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
            }

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
        $prices = RoomType::whereCompanyId($request->company_id)->whereName($request->roomType)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->checkin, $this->checkOutDate($request->checkout));

        $eachRoomDiscount = $discount / count($period);

        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->holiday_price - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->holiday_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                    "discount" => $eachRoomDiscount,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekend_price - $eachRoomDiscount, $request->company_id)['total_with_tax'],
                    "tax" => $this->getRoomTax($prices->weekend_price - $eachRoomDiscount, $request->company_id)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                    "discount" => $eachRoomDiscount,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekday_price - $eachRoomDiscount, $request->company_id)['total_with_tax'],
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
            $model = RoomType::find($id);
            $model->update($request->all());


            // if ($wordpress_push_prices_url != '')
            // {

            //     try {
            //         $curl = curl_init($wordpress_push_prices_url);

            //         curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //         $api_response = curl_exec($curl);
            //         curl_close($curl);

            //         return  $api_response;
            //     } catch (\Throwable $th) {
            //         //throw $th;
            //     }

            // }

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
