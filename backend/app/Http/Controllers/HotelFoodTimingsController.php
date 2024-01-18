<?php

namespace App\Http\Controllers;

//use App\Http\Requests\Allowance\StoreRequest;

use App\Http\Requests\HotelFoodTimings\StoreRequest;
use App\Http\Requests\HotelFoodTimings\UpdateRequest;

use App\Models\HotelFoodTimings;

use Illuminate\Http\Request;


class HotelFoodTimingsController extends Controller
{
    public function index(Request $request)
    {
        $model = HotelFoodTimings::query();


        $model = $model->where('company_id', $request->company_id);

        //datatable Filters
        if ($request->filled('name')) {
            $model->where('name', 'like', "$request->name%");
        }
        if ($request->filled('start_hour')) {
            $model->where('start_hour', 'like', "$request->start_hour%");
        }
        if ($request->filled('end_hour')) {
            $model->where('end_hour', 'like', "$request->end_hour%");
        }

        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';

            $model->orderBy($request->sortBy, $sortDesc);
        } else {
            $model->orderBy('display_order', 'ASC');
        }

        return $model->paginate($request->per_page);
    }

    public function getHotelMenuTimingsList(Request $request)
    {
        $model = HotelFoodTimings::query();
        $model->where('company_id', $request->company_id);
        $model->orderBy("name", "ASC");
        return $model->get();
    }
    public function updateMenuDisplayOrder(Request $request)
    {

        try {

            foreach ($request->order_items as $key => $value) {

                HotelFoodTimings::where('company_id', $request->company_id)
                    ->where('id', $value['id'])
                    ->update(["display_order" => $value['display_order']]);
            }

            return $this->response('Display Order updated successfully', 200, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isRoomExist = HotelFoodTimings::where('name', $request->name)
                    ->where('company_id', $request->company_id)
                    ->first();

                if ($isRoomExist) {
                    if ($isRoomExist->id != $id) {
                        return $this->response($request->name . ' Time Details are already Exist', null, false);
                    }
                }
                $status = HotelFoodTimings::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Time Details are updated succesfully', $status, true);
                } else {
                    return $this->response('Time Details are not Updated', $status, false);
                }
            } else {
                return $this->response('Error Occured', $data, false);
            }
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }



    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsRoom = HotelFoodTimings::where('company_id', $request->company_id)->where('name', $request->name)->count();
                if ($verifyIsRoom == 0) {

                    $record = HotelFoodTimings::create($data);

                    if ($record) {
                        return $this->response('Time details are successfully created', $record, true);
                    } else {
                        return $this->response('Time details not created', $record, false);
                    }
                } else {
                    return $this->response($request->name . ' : Time   is already exist. ', $data, false);
                }
            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show($id)
    {
        return HotelFoodTimings::where('id', $id)->first();
    }
    public function destroy($id)
    {
        if (HotelFoodTimings::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
