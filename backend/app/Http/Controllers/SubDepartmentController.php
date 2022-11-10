<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubDepartment\SubDepartmentRequest;
use App\Http\Requests\SubDepartment\SubDepartmentUpdateRequest;
use App\Models\SubDepartment;
use Illuminate\Http\Request;

class SubDepartmentController extends Controller
{
    public function index(Request $request, SubDepartment $model)
    {
        return $model->with('department')->where('company_id', $request->company_id)->paginate($request->per_page);
    }

    public function search(SubDepartment $model, Request $request, $key)
    {
        $model->where('id', 'LIKE', "%$key%");

        $model->orWhere('name', 'LIKE', "%$key%");

        return $model->with('department')->paginate($request->per_page);
    }

    public function store(SubDepartment $model, SubDepartmentRequest $request)
    {
        $data = $request->validated();

        try {
            $record = $model->create($data);

            if ($record) {
                return $this->response('Sub Department successfully added.', $record->with('department'), true);
            } else {
                return $this->response('Sub Department cannot add.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function show(SubDepartment $SubDepartment)
    {
        return $SubDepartment->with('department');
    }

    public function update(SubDepartmentUpdateRequest $request, SubDepartment $SubDepartment)
    {
        try {
            $record = $SubDepartment->update($request->validated());

            if ($record) {
                return $this->response('Sub Department successfully updated.', $SubDepartment->with('department'), true);
            } else {
                return $this->response('Sub Department cannot update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function destroy(SubDepartment $SubDepartment)
    {
        try {
            $record = $SubDepartment->delete();

            if ($record) {
                return $this->response('Sub Department successfully deleted.', null, true);
            } else {
                return $this->response('Sub Department cannot delete.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function deleteSelected(SubDepartment $model, Request $request)
    {
        try {
            $record = $model->whereIn('id', $request->ids)->delete();

            if ($record) {
                return $this->response('Sub Department successfully deleted.', null, true);
            } else {
                return $this->response('Sub Department cannot delete.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function sub_departments_by_department(SubDepartment $model, Request $request)
    {
        return $model->where('department_id', $request->department_id)->get();
    }

    public function sub_departments_by_departments(SubDepartment $model, Request $request)
    {
        return $model->whereIn('department_id', $request->department_ids ?? [])->get();
    }
}
