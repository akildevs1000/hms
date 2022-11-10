<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Qualification;
use App\Http\Requests\Qualification\StoreRequest;

class QualificationController extends Controller
{
    public function store(Qualification $model, StoreRequest $request)
    {
        $record = $model->updateOrCreate(['employee_id' => $request->employee_id], $request->validated());

        return $this->process('added', $record, class_basename($model));
    }

    public function show(Qualification $model, $id)
    {
        return $model->whereEmployeeId($id)->first();
    }
}
