<?php

namespace App\Http\Controllers;

use App\Http\Requests\Weekend\StoreRequest;
use App\Http\Requests\Weekend\UpdateRequest;
use App\Models\Weekend;

class WeekendController extends Controller
{
    public $name = 'Weekend';
    
    public function index()
    {
        return Weekend::get();
    }

    public function store(StoreRequest $request)
    {
        $model = Weekend::query();

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
        return $this->response(null, Weekend::find($id), true);
    }

    public function update(UpdateRequest $request, $id)
    {
        $model = Weekend::query();

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

            $record = Weekend::find($id)->delete();

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
