<?php

namespace App\Http\Controllers;

use App\Http\Requests\Weekday\StoreRequest;
use App\Http\Requests\Weekday\UpdateRequest;
use App\Models\Weekday;

class WeekdayController extends Controller
{
    public $name = 'Weekday';
    
    public function index()
    {
        return Weekday::get();
    }

    public function store(StoreRequest $request)
    {
        $model = Weekday::query();

        $model->where('company_id', $request->company_id)->where('day', $request->day);

        if ($model->exists()) {
            return $this->response($this->name . ' Successfully created.', $model->first(), true);
        }

        try {

            $record = $model->create($request->validated());

            if ($record) {
                return $this->response($this->name . ' Successfully created.', $record, true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function show($id)
    {
        return $this->response(null, Weekday::find($id), true);
    }

    public function update(UpdateRequest $request, $id)
    {
        $model = Weekday::query();

        try {

            $record = $model->where("id", $id)->update($request->validated());

            if ($record) {
                return $this->response($this->name . ' Successfully update.', $model->find($id), true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy($id)
    {
        try {

            $record = Weekday::find($id)->delete();

            if ($record) {
                return $this->response($this->name . ' Successfully deleted.', null, true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
