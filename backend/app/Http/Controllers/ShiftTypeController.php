<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShiftType\StoreRequest;
use App\Http\Requests\ShiftType\UpdateRequest;
use App\Models\ShiftType;
use Illuminate\Http\Request;

class ShiftTypeController extends Controller
{
    public function index(Request $request, ShiftType $model)
    {
        return $model->with('shift')->get();
    }

    public function store(ShiftType $model, StoreRequest $request)
    {
        $data = $request->validated();

        try {
            $record = $model->create($data);

            if ($record) {
                return $this->response('ShiftType successfully added.', $record, true);
            } else {
                return $this->response('ShiftType cannot add.', null, false);
            }

        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request, ShiftType $ShiftType)
    {
        try {
            $record = $ShiftType->update($request->validated());

            if ($record) {
                return $this->response('ShiftType successfully updated.', $ShiftType->with('children'), true);
            } else {
                return $this->response('ShiftType cannot update.', null, false);
            }

        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function destroy(ShiftType $ShiftType)
    {
        try {
            $record = $ShiftType->delete();

            if ($record) {
                return $this->response('ShiftType successfully deleted.', null, true);
            } else {
                return $this->response('ShiftType cannot delete.', null, false);
            }

        } catch (\Throwable$th) {
            throw $th;
        }
    }

}
