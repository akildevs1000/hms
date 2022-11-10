<?php

namespace App\Http\Controllers;

use App\Http\Requests\Emirate\StoreRequest;
use App\Models\EmiratesInfo;

class EmiratesController extends Controller
{
    public function store(EmiratesInfo $model, StoreRequest $request)
    {
        $record = $model->updateOrCreate(['employee_id' => $request->employee_id], $request->validated());

        return $this->process('added', $record, class_basename($model));
    }

    public function show(EmiratesInfo $model, $id)
    {
        return $model->whereEmployeeId($id)->first();
    }
}
