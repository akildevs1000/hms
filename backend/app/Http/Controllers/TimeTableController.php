<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeTable\StoreRequest;
use App\Http\Requests\TimeTable\UpdateRequest;
use App\Models\TimeTable;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TimeTable $model)
    {
        return $model->paginate($request->per_page);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, TimeTable $model)
    {
        try {
            $record = $model->create($request->validated());

            if ($record) {
                return $this->response('TimeTable successfully added.', $record, true);
            } else {
                return $this->response('TimeTable cannot add.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeTable  $timeTable
     * @return \Illuminate\Http\Response
     */
    public function show(TimeTable $timeTable)
    {
        return $timeTable;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeTable  $timeTable
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, TimeTable $timeTable)
    {

        try {
            $record = $timeTable->update($request->validated());

            if ($record) {
                return $this->response('TimeTable successfully updated.', $timeTable, true);
            } else {
                return $this->response('TimeTable cannot update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeTable  $timeTable
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeTable $timeTable)
    {
        try {
            if ($timeTable->delete()) {
                return $this->response('TimeTable successfully updated.', null, true);
            } else {
                return $this->response('TimeTable cannot update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
}
