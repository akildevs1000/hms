<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shift\StoreRequest;
use App\Http\Requests\Shift\UpdateRequest;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Shift::query();
        $model->with('time_table', "shift_type");
        $model->where('company_id', $request->company_id);
        return $model->paginate($request->per_page);
    }

    public function shift_by_type(Request $request)
    {
        return Shift::with("shift_type")->where("company_id", $request->company_id)->where("shift_type_id", $request->shift_type_id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Shift $model)
    {
        try {
            $record = $model->create($request->validated());

            if ($record) {
                return $this->response('Shift successfully added.', $record, true);
            } else {
                return $this->response('Shift cannot add.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shift  $Shift
     * @return \Illuminate\Http\Response
     */
    public function show(Shift $Shift)
    {
        return $Shift;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shift  $Shift
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Shift $Shift)
    {
        try {
            $record = $Shift->update($request->validated());

            if ($record) {
                return $this->response('Shift successfully updated.', $record, true);
            } else {
                return $this->response('Shift cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shift  $Shift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $Shift)
    {
        try {
            if ($Shift->delete()) {
                return $this->response('Shift successfully updated.', null, true);
            } else {
                return $this->response('Shift cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
