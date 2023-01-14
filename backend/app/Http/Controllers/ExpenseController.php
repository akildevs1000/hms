<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;

class ExpenseController extends Controller
{
    protected $model_name = 'Expense';
    protected $model;
    protected $name;

    public function __construct()
    {
        $this->model = new Expense();
        $this->name = class_basename($this->model);
    }

    public function index(Request $request)
    {
        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $model->orderByDesc("id");
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            $model->whereDate('created_at', '>=', $from);
            $model->whereDate('created_at', '<=', $to);
        }

        if ($request->is_account && $request->has('is_account') && $request->filled('is_account')) {
            return  $model->get();
        }

        return  $model->paginate($request->per_page ?? 20);
    }

    public function search(Request $request, $key)
    {
        $model  = Expense::query();
        $fields = [
            'voucher',
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

    public function counts(Request $request)
    {
        // ['id' => 1, 'name' => 'Cash'],
        // ['id' => 2, 'name' => 'Card'],
        // ['id' => 3, 'name' => 'Online'],
        // ['id' => 4, 'name' => 'Bank'],
        // ['id' => 5, 'name' => 'UPI'],
        // ['id' => 6, 'name' => 'Cheque']
        // ['id' => 7, 'name' => 'City Ledger']



        $expense = $this->model
            ->where('company_id', $request->company_id)
            ->orderByDesc("id");

        $income = Payment::query()
            ->where('company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->orderByDesc("id");

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            $expense->whereDate('created_at', '>=', $from);
            $expense->whereDate('created_at', '<=', $to);

            $income->whereDate('created_at', '>=', $from);
            $income->whereDate('created_at', '<=', $to);
        }


        $loss =   $expense->clone()->sum('amount') - $income->clone()->sum('amount');
        $profit = $income->clone()->sum('amount') - $expense->clone()->sum('amount');

        return [
            'expense' => [
                'Cash' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 1))->sum('amount'),
                'Card' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 2))->sum('amount'),
                'Online' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 3))->sum('amount'),
                'Bank' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 4))->sum('amount'),
                'UPI' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 5))->sum('amount'),
                'Cheque' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 6))->sum('amount'),
                'OverallTotal' => $expense->clone()->sum('amount'),
            ],
            'income' => [
                'Cash' => $this->getSumByModel($income, 1),
                'Card' => $this->getSumByModel($income, 2),
                'Online' => $this->getSumByModel($income, 3),
                'Bank' => $this->getSumByModel($income, 4),
                'UPI' => $this->getSumByModel($income, 5),
                'Cheque' => $this->getSumByModel($income, 6),
                'City_ledger' => $this->getSumByModel($income, 7),
                'OverallTotal' => $income->clone()->sum('amount'),
            ],

            'profit' =>  $profit > 0 ? $profit . '.00' : 0 . '.00',
            'loss' =>  $loss > 0 ? $loss . '.00' : 0 . '.00',
        ];
    }

    public function getSumByModel($model, $id)
    {
        return $model->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', $id))->sum('amount');
    }
}