<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vendors\StoreRequest;
use App\Http\Requests\Vendors\UpdateRequest;
use App\Models\Vendors;
use Illuminate\Http\Request;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function vendorsDropdownList()
    {
        $model = Vendors::query();
        $model->where('company_id', request('company_id'));
        $model->orderBy("name", "asc");
        return $model->get();
    }
    public function index(Request $request)
    {
        $model = Vendors::query();


        $model = $model->where('company_id', $request->company_id);


        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';
            if (strpos($request->sortBy, '.')) {
            } else {
                $model->orderBy($request->sortBy, $sortDesc);
            }
        } else {
            $model->orderBy('name', 'ASC');
        }

        return $model->paginate($request->per_page);
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
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $isVendorAlreadyExist = Vendors::where('company_id', $request->company_id)

                    ->where('name',  $request->name)

                    ->count();
                if ($isVendorAlreadyExist == 0) {

                    $record = Vendors::create($data);

                    if ($record) {
                        return $this->response('Vendor details are successfully created', $record, true);
                    } else {
                        return $this->response('CategoVendorry details not created', $record, false);
                    }
                } else {
                    return $this->response($request->name  . ' : Vendor   is already exist. ', $data, false);
                }
            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendors  $vendors
     * @return \Illuminate\Http\Response
     */
    public function show(Vendors $vendors, $id)
    {
        return Vendors::where('id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendors  $vendors
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendors $vendors)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendors  $vendors
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isTaxSlabsExist = Vendors::where('company_id', $request->company_id)
                    ->where('name',  $request->name)
                    ->first();

                if ($isTaxSlabsExist) {
                    if ($isTaxSlabsExist->id != $id) {
                        return $this->response($request->start_price . '-' . $request->end_price . ' Category  Details are Can not save', null, false);
                    }
                }
                $status = Vendors::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Vendor Details are updated succesfully', $status, true);
                } else {
                    return $this->response('Vendor Details are not Updated', $status, false);
                }
            } else {
                return $this->response('Error Occured', $data, false);
            }
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendors  $vendors
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendors $vendors, $id)
    {
        if (Vendors::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
