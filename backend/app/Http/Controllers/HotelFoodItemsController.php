<?php

namespace App\Http\Controllers;

//use App\Http\Requests\Allowance\StoreRequest;

use App\Http\Requests\HotelFoodItems\StoreRequest;
use App\Http\Requests\HotelFoodItems\UpdateRequest;

use App\Models\HotelFoodItems;
use App\Models\HotelFoodItemsCategories;
use App\Models\HotelFoodItemsTimings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelFoodItemsController extends Controller
{
    public function index(Request $request)
    {
        $model = HotelFoodItems::with(["category", "timings"]);


        $model = $model->where('company_id', $request->company_id);

        //datatable Filters
        if ($request->filled('name')) {
            $model->where('name', 'like', "$request->name%");
        }
        if ($request->filled('description')) {
            $model->where('description', 'like', "%$request->description%");
        }
        if ($request->filled('category_id')) {
            $model->where('category_id',   $request->category_id);
        }
        if ($request->filled('timing_id')) {
            $model->where('timing_id',   $request->timing_id);
        }
        if ($request->filled('ingredients')) {
            $model->where('ingredients', 'like', "%$request->ingredients%");
        }
        if ($request->filled('is_non_veg')) {
            $model->where('is_non_veg',   $request->is_non_veg);
        }
        if ($request->filled('amount')) {
            $model->where('amount',   $request->amount);
        }
        if ($request->filled('status')) {
            $model->where('status',   $request->status);
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
    public function updateMenuDisplayOrder(Request $request)
    {

        try {

            foreach ($request->order_items as $key => $value) {

                HotelFoodItems::where('company_id', $request->company_id)
                    ->where('id', $value['id'])
                    ->update(["display_order" => $value['display_order']]);
            }

            return $this->response('Display Order updated successfully', 200, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    public function hotelFoodDrodownList(Request $request)
    {
        $model = HotelFoodItems::with(["category", "timings"]);


        $model = $model->where('company_id', $request->company_id);
        return $model->orderBy('name', 'ASC')->get();
    }
    public function getHotelMenuItemsList(Request $request)
    {
        $model = HotelFoodItems::query();
        $model->where('company_id', $request->company_id);
        $model->orderBy("name", "ASC");
        return $model->get();
    }


    public function update(UpdateRequest $request, $id)
    {
        //try {

        $data = $request->validated();
        $timing_ids = $data['timing_id'];
        // unset($data['timing_id']);


        if ($data) {

            if ($request->hasFile('image')) {

                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = $id . '.' . $ext;

                $folder = 'public/hotel/' . $request->company_id . '/food_menu';
                if (!Storage::disk('public')->exists($folder)) {
                    Storage::disk('public')->makeDirectory($folder);
                }



                $file->storeAs($folder, $fileName);
            }

            $isNameExist = HotelFoodItems::where('name', $request->name)
                ->where('company_id', $request->company_id)
                ->first();

            if ($isNameExist) {
                if ($isNameExist->id != $id) {
                    return $this->response($request->name . ' Name Details are already Exist', null, false);
                }
            }
            $status = HotelFoodItems::whereId($id)->update($data);

            $categoriesArray = explode(',', $timing_ids);
            HotelFoodItemsTimings::where('company_id', $request->company_id)->where('item_id', $id)->delete();
            foreach ($categoriesArray as  $value) {
                HotelFoodItemsTimings::create(
                    ['company_id' => $request->company_id, 'item_id' => $id, 'category_id' => $value]
                );
            }



            if ($status) {
                return $this->response('Item Details are updated succesfully', $status, true);
            } else {
                return $this->response('Item Details are not Updated', $status, false);
            }
        } else {
            return $this->response('Error Occured', $data, false);
        }
        // } catch (\Throwable $th) {
        //     return $this->response('Something wrong.', $th, false);
        // }
    }



    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            $timing_ids = $data['timing_id'];
            // unset($data['timing_id']);

            if ($data) {

                $verifyIsRoom = HotelFoodItems::where('company_id', $request->company_id)->where('name', $request->name)->count();
                if ($verifyIsRoom == 0) {

                    $record = HotelFoodItems::create($data);


                    $categoriesArray = explode(',', $timing_ids);
                    HotelFoodItemsTimings::where('company_id', $request->company_id)->where('item_id', $request->id)->delete();
                    foreach ($categoriesArray as  $value) {
                        HotelFoodItemsTimings::create(
                            ['company_id' => $request->company_id, 'item_id' => $record->id, 'category_id' => $value]
                        );
                    }

                    if ($request->hasFile('image')) {

                        $file = $request->file('image');
                        $ext = $file->getClientOriginalExtension();
                        $fileName =  $record->id . '.' . $ext;
                        $folder = 'public/hotel/' . $request->company_id . '/food_menu';
                        if (!Storage::disk('public')->exists($folder)) {
                            Storage::disk('public')->makeDirectory($folder);
                        }
                        $file->storeAs($folder, $fileName);
                    }

                    if ($record) {
                        return $this->response('Item details are successfully created', $record, true);
                    } else {
                        return $this->response('Item details not created', $record, false);
                    }
                } else {
                    return $this->response($request->name . ' : Item   is already exist. ', $data, false);
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
        return HotelFoodItems::where('id', $id)->first();
    }
    public function destroy($id)
    {
        if (HotelFoodItems::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
