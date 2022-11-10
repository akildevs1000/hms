<?php

namespace App\Http\Controllers;

use App\Http\Requests\Leave\StoreRequest;
use App\Http\Requests\Leave\UpdateRequest;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Leave $model, Request $request)
    {
        return $model
            ->with('approvedBy')
            ->where('company_id', $request->company_id)
            ->where('employee_id', $request->employee_id)
            ->paginate($request->per_page ?? 10);
    }

    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->company_id) {
                $data['company_id'] = $request->company_id;
            }
            $record = Leave::create($data);

            if ($record) {
                return $this->response('Leave Successfully created.', $record, true);
            } else {
                return $this->response('Leave cannot create.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request, Leave $Leave)
    {
        try {
            $record = $Leave->update($request->all());

            if ($record) {
                return $this->response('Leave successfully updated.', $record, true);
            } else {
                return $this->response('Leave cannot update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function destroy(Leave $Leave)
    {
        $record = $Leave->delete();

        if ($record) {
            return $this->response('Leave successfully deleted.', $record, true);
        } else {
            return $this->response('Leave cannot delete.', null, false);
        }
    }

    public function search(Leave $model, Request $request, $key)
    {
        return $model->where('title', 'LIKE', "%$key%")->paginate($request->per_page);
    }

    public function deleteSelected(Request $request)
    {
        $record = Leave::whereIn('id', $request->ids)->delete();

        if ($record) {
            return $this->response('Leave Successfully created.', $record, true);
        } else {
            return $this->response('Leave cannot create.', null, false);
        }
    }

    public function geLeaveNotification($id, Request $request)
    {
        return Leave::with('employee', 'approvedBy')
            ->where('company_id', $request->company_id)
            ->whereJsonContains('supervisor', (int) $id)
            ->paginate($request->per_page ?? 5);
    }

    public function status(Request $request)
    {
        $record = Leave::where('id', $request->id)->update([
            'is_approved' => $request->is_approved,
            'approved_by' => $request->approved_by,
            'approved_date' => now(),
        ]);

        if ($record) {
            return $this->response('Leave Successfully updated.', Leave::with('employee', 'approvedBy')->find($request->id), true);
        } else {
            return $this->response('Leave cannot updated.', null, false);
        }
    }

    public function searchNotification(Leave $model, Request $request, $key, $id)
    {
        return $model->where('title', 'LIKE', "%$key%")
            ->where('employee_id', $key)
            ->whereJsonContains('supervisor', (int) $id)
            ->paginate($request->per_page ?? 10);
    }
}
