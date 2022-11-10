<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\DepartmentRequest;
use App\Http\Requests\Department\DepartmentUpdateRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request, Department $model)
    {
        return $model->with('children')->where('company_id', $request->company_id)->paginate($request->per_page);
    }

    public function search(Request $request, $key)
    {
        $model = Department::query();
        $model->where('id', 'LIKE', "%$key%");
        $model->where('company_id', $request->company_id);
        $model->orWhere('name', 'LIKE', "%$key%");
        return $model->with('children')->paginate($request->per_page);
    }

    public function store(Department $model, DepartmentRequest $request)
    {
        $data = $request->validated();

        if ($request->company_id) {
            $data["company_id"] = $request->company_id;
        }
        try {
            $record = $model->create($data);

            if ($record) {
                return $this->response('Department successfully added.', $record->with('children'), true);
            } else {
                return $this->response('Department cannot add.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(Department $Department)
    {
        return $Department->with('children');
    }

    public function update(DepartmentUpdateRequest $request, Department $Department)
    {
        try {
            $record = $Department->update($request->validated());

            if ($record) {
                return $this->response('Department successfully updated.', $Department->with('children'), true);
            } else {
                return $this->response('Department cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy(Department $Department)
    {
        try {
            $record = $Department->delete();

            if ($record) {
                return $this->response('Department successfully deleted.', null, true);
            } else {
                return $this->response('Department cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteSelected(Department $model, Request $request)
    {
        try {
            $record = $model->whereIn('id', $request->ids)->delete();

            if ($record) {
                return $this->response('Department successfully deleted.', null, true);
            } else {
                return $this->response('Department cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}