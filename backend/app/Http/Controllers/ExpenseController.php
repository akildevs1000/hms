<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $model = new Expense();
    protected $model_name = 'Expense';
    
    public function index(Request $request)
    {
        return $this->model->where('company_id', $request->company_id)->orderByDesc("id")->paginate($request->per_page);
    }


    public function store(Expense $model, StoreRequest $request)
    {
        try {
            $record = Expense::create($request->validated());

            if ($record) {
                return $this->response($this->name . ' Successfully created.', $record, true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(Expense $expense)
    {
        //
    }

    public function update(UpdateRequest $request, Expense $expense)
    {
        try {
            $record = $expense->update($request->all());

            if ($record) {
                return $this->response($this->name . ' successfully updated.', $record, true);
            } else {
                return $this->response($this->name . ' cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy(Expense $expense)
    {
        $record = $expense->delete();

        if ($record) {
            return $this->response($this->name . ' successfully deleted.', $record, true);
        } else {
            return $this->response($this->name . ' cannot delete.', null, false);
        }
    }
}
