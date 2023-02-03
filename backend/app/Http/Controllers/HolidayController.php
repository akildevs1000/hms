<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Requests\Holiday\StoreRequest;
use App\Http\Requests\Holiday\UpdateRequest;

class HolidayController extends Controller
{
    public $name = 'Holiday';

    public function index(Request $request)
    {
        return Holiday::whereCompanyId($request->company_id)->paginate($request->per_page ?? 20);
    }

    public function store(StoreRequest $request)
    {
        $model = Holiday::query();

        $data = [
            'description' => $request->description,
            'company_id' => $request->company_id,
        ];

        $d1 =  strtotime($request->dates[0]);
        $d2 = strtotime($request->dates[1]);



        if ($d2 > $d1) {
            $data["from"] = $request->dates[0];
            $data["to"] = $request->dates[1];
        } else {
            $data["from"] = $request->dates[1];
            $data["to"] = $request->dates[0];
        }
        $model->where('company_id', $request->company_id)->whereDate('from', $data["from"])->whereDate('to', $data["to"]);

        if ($model->exists()) {
            return $this->response($this->name . ' Successfully created.', $model->first(), true);
        }
        try {
            $record = $model->create($data);
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
        return $this->response(null, Holiday::find($id), true);
    }

    public function update(UpdateRequest $request, $id)
    {
        $model = Holiday::query();

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

            $record = Holiday::find($id)->delete();

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