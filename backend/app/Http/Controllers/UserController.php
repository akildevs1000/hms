<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // $model = User::query();

        // if ($request->user_type == "employee") {
        //     $model->where('id', $request->id);
        // }

        // $model->where('company_id', $request->company_id);
        // return $model->paginate(20);

        $sortBy = $request->input('sortBy');
        $sortDesc = $request->input('sortDesc');
        $itemsPerPage = $request->input('itemsPerPage');

        $model = User::with(['role']);

        // $model->when()

        if ($request->filled('name') && $request->has('name')) {
            $model->Where('name', 'ILIKE', '%' . $request->name . '%');
        }

        if ($request->filled('email') && $request->email != "") {
            $model->Where('email', 'ILIKE', '%' . $request->email . '%');
        }

        if ($request->filled('mobile') && $request->mobile != "") {
            $model->Where('mobile', 'ILIKE', '%' . $request->mobile . '%');
        }

        if ($sortBy && $sortDesc) {
            $model->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $desserts = $model->paginate($itemsPerPage);

        return $desserts;

    }

    public function store(User $model, StoreRequest $request)
    {

        try {
            $fileName = '';
            $data = $request->validated();
            $data["password"] = Hash::make($data["password"]);
            $data["employee_role_id"] = 1;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $path = $file->storeAs('public/user/images', $fileName);
                $data["image"] = $fileName;
            }

            $record = $model->create($data);

            if ($record) {
                return $this->response('User successfully added.', $record, true);
            } else {
                return $this->response('User cannot add.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(User $user, UpdateRequest $request)
    {

        try {
            $data = $request->validated();

            if ($request->password) {
                $data["password"] = Hash::make($data["password"]);
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $path = $file->storeAs('public/user/images', $fileName);
                $data["image"] = $fileName;
            }

            $record = $user->update($data);

            if ($record) {
                return $this->response('User successfully updated.', $record, true);
            } else {
                return $this->response('User cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(User $User)
    {
        return $User;
    }

    public function destroy(User $user)
    {
        try {
            $record = $user->delete();

            if ($record) {
                return $this->response('User successfully deleted.', null, true);
            } else {
                return $this->response('User cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteSelected(User $model, Request $request)
    {
        try {
            $record = $model->whereIn('id', $request->ids);

            if ($record) {
                return $this->response('Select record successfully deleted.', null, true);
            } else {
                return $this->response('Select record cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function search(User $model, Request $request, $key)
    {
        $model = $this->FilterCompanyList($model, $request);

        $fields = [
            'name',
            'role' => ['name'],
        ];

        $model = $this->process_search($model, $key, $fields);

        $model->whereHas("role", function ($q) {
            return $q->where('name', '!=', "company");
        });

        $model->with("role");

        return $model->paginate($request->per_page);
    }
}
