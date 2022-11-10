<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Http\Requests\Experience\StoreRequest;

class ExperienceController extends Controller
{
    public function store(Experience $model, StoreRequest $request)
    {
        $record = $model->updateOrCreate(['employee_id' => $request->employee_id], $request->validated());

        return $this->process('added', $record, class_basename($model));
    }

    public function show(Experience $model, $id)
    {
        return $model->whereEmployeeId($id)->first();
    }
}
