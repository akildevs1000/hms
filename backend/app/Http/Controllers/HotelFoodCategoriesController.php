<?php

namespace App\Http\Controllers;

//use App\Http\Requests\Allowance\StoreRequest;

use App\Http\Requests\HotelFoodCategories\StoreRequest;
use App\Http\Requests\HotelFoodCategories\UpdateRequest;

use App\Models\HotelFoodCategories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelFoodCategoriesController extends Controller
{
    public function index(Request $request)
    {
        $model = HotelFoodCategories::query();

        $model = $model->withCount('items');


        $model = $model->where('company_id', $request->company_id);

        //datatable Filters
        if ($request->filled('name')) {
            $model->where('name', 'like', "$request->name%");
        }
        if ($request->filled('description')) {
            $model->where('description', 'like', "%$request->description%");
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

    public function getHotelMenuCategoriesList(Request $request)
    {
        $model = HotelFoodCategories::query();
        $model->where('company_id', $request->company_id);
        $model->orderBy("display_order", "ASC");
        return $model->get();
    }

    public function updateMenuDisplayOrder(Request $request)
    {

        try {

            foreach ($request->order_items as $key => $value) {

                HotelFoodCategories::where('company_id', $request->company_id)
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
        // try {



        $data =    $request->validated();



        if ($data) {

            $isRoomExist = HotelFoodCategories::where('name', $request->name)
                ->where('company_id', $request->company_id)
                ->first();

            if ($isRoomExist) {
                if ($isRoomExist->id != $id) {
                    return $this->response($request->name . ' Category Details are already Exist', null, false);
                }
            }
            $status = HotelFoodCategories::whereId($id)->update($data);



            if ($request->hasFile('image')) {

                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = $id . '.jpg';

                // $folder = 'public/hotel/categories/' . $request->company_id . '';


                // if (!Storage::disk('public')->exists($folder)) {
                //     Storage::disk('public')->makeDirectory($folder);
                // }

                // $file->storeAs($folder, $fileName);

                $folder = 'hotel/categories/' . $request->company_id;
                $destinationPath = public_path($folder);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $fileName);
            }




            if ($status) {
                return $this->response('Category Details are updated succesfully', $status, true);
            } else {
                return $this->response('Category Details are not Updated', $status, false);
            }
        } else {
            return $this->response('Error Occured', $data, false);
        }
        // } catch (\Throwable $th) {
        //     return $this->response('Something wrong.' . $th, $th, false);
        // }
    }



    public function store(StoreRequest $request)
    {

        return $request->all();
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsExist = HotelFoodCategories::where('company_id', $request->company_id)->where('name', $request->name)->count();
                if ($verifyIsExist == 0) {

                    $record = HotelFoodCategories::create($data);

                    if ($record) {

                        if ($request->hasFile('image')) {

                            $file = $request->file('image');
                            $ext = $file->getClientOriginalExtension();
                            $fileName = $record->id . '.jpg';

                            $folder = 'hotel/categories/' . $request->company_id;
                            $destinationPath = public_path($folder);
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            $file->move($destinationPath, $fileName);
                        }







                        return $this->response('Category details are successfully created', $record, true);
                    } else {
                        return $this->response('Category details not created', $record, false);
                    }
                } else {
                    return $this->response($request->name . ' : Category   is already exist. ', $data, false);
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
        return HotelFoodCategories::where('id', $id)->first();
    }
    public function destroy(Request $request, $id)
    {
        if (HotelFoodCategories::find($id)->delete()) {

            $folder = 'hotel/categories/' . $request->company_id;
            $fileName =  $id . '.jpg'; // . $ext;
            $filePath = public_path($folder . '/' . $fileName);

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
