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
        $sortBy = $request->input('sortBy');
        $sortDesc = $request->input('sortDesc');
        $itemsPerPage = $request->input('itemsPerPage');

        $model = Holiday::whereCompanyId($request->company_id);

        if($request->filled('description') && $request->has('description')) {
            $model->Where('description', 'ILIKE', '%' . $request->description . '%');
        }

        if ($sortBy && $sortDesc) {
            $model->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        return   $model->paginate($itemsPerPage ?? 20);

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


            $record = $model->where("id", $id)->update($data);

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
