<?php

namespace App\Http\Controllers;

use App\Http\Requests\Policy\StoreRequest;
use App\Http\Requests\Policy\UpdateRequest;
use App\Models\Policy;
use Illuminate\Http\Request;

class policyController extends Controller
{
    public function index(Policy $model, Request $request)
    {
        return $model->where('company_id', $request->company_id)->paginate($request->per_page ?? 10);
    }

    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->company_id) {
                $data['company_id'] = $request->company_id;
            }
            $record = Policy::create($data);

            if ($record) {
                return $this->response('Policy Successfully created.', $record, true);
            } else {
                return $this->response('Policy cannot create.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request, Policy $Policy)
    {
        try {
            $record = $Policy->update($request->all());

            if ($record) {
                return $this->response('Policy successfully updated.', $record, true);
            } else {
                return $this->response('Policy cannot update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function destroy(Policy $Policy)
    {
        $record = $Policy->delete();

        if ($record) {
            return $this->response('Policy successfully deleted.', $record, true);
        } else {
            return $this->response('Policy cannot delete.', null, false);
        }
    }

    public function search(Policy $model, Request $request, $key)
    {
        return $model->where('title', 'LIKE', "%$key%")->paginate($request->per_page);
    }

    public function deleteSelected(Request $request)
    {
        $record = Policy::whereIn('id', $request->ids)->delete();

        if ($record) {
            return $this->response('Policy Successfully created.', $record, true);
        } else {
            return $this->response('Policy cannot create.', null, false);
        }
    }
}
