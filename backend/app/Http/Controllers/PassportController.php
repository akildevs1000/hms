<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;
use App\Http\Requests\Passport\StoreRequest;

class PassportController extends Controller
{
    public function store(Passport $model, StoreRequest $request)
    {
        $record = $model->updateOrCreate(['employee_id' => $request->employee_id], $request->validated());

        return $this->process('added', $record, class_basename($model));
    }

    public function show(Passport $model, $id)
    {
        return $model->whereEmployeeId($id)->first();
    }
}
