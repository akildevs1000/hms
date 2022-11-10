<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Activity $model, Request $request)
    {
        return $model->with('employee:id,first_name')->orderByDesc("id")->paginate($request->per_page ?? 10);
    }

    public function store(Request $request)
    {
        try {
            $record = Activity::create($request->all());

            if ($record) {
                return $this->response('Activity Successfully created.', $record, true);
            } else {
                return $this->response('Activity cannot create.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
}
