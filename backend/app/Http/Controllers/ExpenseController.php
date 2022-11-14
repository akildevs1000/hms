<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $model_name = 'Expense';
    protected $model;

    public function __construct()
    {
        $this->model = new Expense();
        $this->name = class_basename($this->model);
    }

    public function index(Request $request)
    {
        return $this->model->where('company_id', $request->company_id)->orderByDesc("id")->paginate($request->per_page);

    }

    public function search(Request $request, $key)
    {

        $model  = Expense::query();
        $fields = [
            'item',
            'amount',
            'payment_modes',
        ];
        $model = $this->process_search($model, $key, $fields);
        return $model->where("company_id", $request->company_id)->paginate($request->per_page);
    }


    public function store(StoreRequest $request)
    {
        try {
            $record = $this->model->create($request->validated());

            if ($record) {
                return $this->response($this->name . ' Successfully created.', $record, true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request, Expense $expense)
    {
        try {
            $record = $expense->update($request->validated());

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
