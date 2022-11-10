<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeLicense\StoreRequest;
use App\Models\TradeLicense;

class TradeLicenseController extends Controller
{
    public function store(TradeLicense $model, StoreRequest $request, $id)
    {
        $record = $model->updateOrCreate(['company_id' => $id], $request->validated());

        return $this->process('added', $record, class_basename($model));
    }

    public function show(TradeLicense $model, $id)
    {
        return $model->whereEmployeeId($id)->first();
    }
}
