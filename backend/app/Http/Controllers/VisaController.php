<?php

namespace App\Http\Controllers;

use App\Models\Visa;
use App\Http\Requests\Visa\StoreRequest;

class VisaController extends Controller
{
    public function store(Visa $model, StoreRequest $request)
    {
        $record = $model->updateOrCreate(['employee_id' => $request->employee_id], $request->validated());

        return $this->process('added', $record, class_basename($model));
    }

    public function show(Visa $model, $id)
    {
        return $model->whereEmployeeId($id)->first();
    }
}
