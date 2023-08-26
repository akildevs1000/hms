<?php

namespace App\Http\Controllers;

use App\Models\TaxSlabs;
use App\Http\Requests\TaxSlab\StoreRequest;
use App\Http\Requests\TaxSlab\UpdateRequest;
use Illuminate\Http\Request;

class TaxSlabsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = TaxSlabs::query();
        if ($request->filled('status') && $request->status != "-1") {
            $model->where('status', $request->status);
        }

        $model = $model->where('company_id', $request->company_id);

        //datatable Filters
        if ($request->filled('TaxSlabs_no')) {
            $model->where('TaxSlabs_no', 'like', "$request->TaxSlabs_no%");
        }
        if ($request->filled('TaxSlabs_type')) {
            $model->where('TaxSlabs_type_id', $request->TaxSlabs_type);
        }
        if ($request->filled('floor_no')) {
            $model->where('floor_no', $request->floor_no);
        }
        if ($request->filled('status')) {
            $model->where('status', $request->status);
        }
        //  else {
        //     $model->where('status', 0);
        // }

        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';
            if (strpos($request->sortBy, '.')) {
            } else {
                $model->orderBy($request->sortBy, $sortDesc);
            }
        } else {
            $model->orderBy('start_price', 'ASC');
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
     * @param  \App\Http\Requests\StoreTaxSlabsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsTaxSlabs = TaxSlabs::where('company_id', $request->company_id)
                    ->where(function ($query) use ($request) {
                        $query->orwhere('start_price', '<=', $request->start_price)->where('end_price', '>=', $request->start_price);
                        $query->orwhere('start_price', '<=', $request->end_price)->where('end_price', '>=', $request->end_price);
                    })
                    ->count();
                if ($verifyIsTaxSlabs == 0) {

                    $record = TaxSlabs::create($data);

                    if ($record) {
                        return $this->response('TaxSlabs details are successfully created', $record, true);
                    } else {
                        return $this->response('TaxSlabs details not created', $record, false);
                    }
                } else {
                    return $this->response($request->start_price . '-' . $request->end_price  . ' : TaxSlabs number is already exist. ', $data, false);
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
     * @param  \App\Models\TaxSlabs  $taxSlabs
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TaxSlabs::where('id', $id)->first();
    }
    public function destroy(TaxSlabs $lostAndFoundItems, $id)
    {
        if (TaxSlabs::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaxSlabs  $taxSlabs
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxSlabs $taxSlabs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaxSlabsRequest  $request
     * @param  \App\Models\TaxSlabs  $taxSlabs
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, TaxSlabs $taxSlabs, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isTaxSlabsExist = TaxSlabs::where('company_id', $request->company_id)
                    ->where(function ($query) use ($request) {
                        $query->orwhere('start_price', '<=', $request->start_price)->where('end_price', '>=', $request->start_price);
                        $query->orwhere('start_price', '<=', $request->end_price)->where('end_price', '>=', $request->end_price);
                    })
                    ->first();

                if ($isTaxSlabsExist) {
                    if ($isTaxSlabsExist->id != $id) {
                        return $this->response($request->start_price . '-' . $request->end_price . ' TaxSlabs Details are Can not save', null, false);
                    }
                }
                $status = TaxSlabs::whereId($id)->update($data);
                if ($status) {
                    return $this->response('TaxSlabs Details are updated succesfully', $status, true);
                } else {
                    return $this->response('TaxSlabs Details are not Updated', $status, false);
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
     * @param  \App\Models\TaxSlabs  $taxSlabs
     * @return \Illuminate\Http\Response
     */
}
