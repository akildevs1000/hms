<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $model_name = 'Expense';
    protected $model;
    protected $name;

    public function __construct()
    {
        $this->model = new Expense();
        $this->name  = class_basename($this->model);
    }

    public function index(Request $request)
    {
        $model = Expense::query();
        $model->where('company_id', $request->company_id);

        if ($request->filled('from') && $request->filled('to')) {
            $model->whereDate('created_at', '>=', $request->from);
            $model->whereDate('created_at', '<=', $request->to);
            $model->orderBy("created_at", 'asc');
        } else {
            $model->orderBy("created_at", 'desc');
        }

        if ($request->is_management == 1 && $request->has('is_management') && $request->filled('is_management')) {
            $model->where('is_management', 1);
        } else {
            $model->where('is_management', 0);
        }

        if ($request->is_account && $request->has('is_account') && $request->filled('is_account')) {
            return $model->get();
        }

        return $model->paginate($request->per_page ?? 50);
    }

    public function managementExpense(Request $request)
    {
        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $model->orderByDesc("id");
        $model->where('is_management', 1);
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to   = $request->to_date;
            $model->whereDate('created_at', '>=', $from);
            $model->whereDate('created_at', '<=', $to);
        }

        if ($request->is_account && $request->has('is_account') && $request->filled('is_account')) {
            return $model->get();
        }
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
                $this->storeDocument($request, $record);
                return $this->response($this->name . ' Successfully created.', $record, true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function update(UpdateRequest $request, Expense $expense)
    {
        try {
            $record = $expense->update($request->validated());
            if ($record) {
                $this->storeDocument($request, $expense);
                return $this->response($this->name . ' successfully updated.', $record, true);
            } else {
                return $this->response($this->name . ' cannot update.', null, false);
            }
        } catch (\Throwable$th) {
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
            $to   = $request->to_date;
            $expense->whereDate('created_at', '>=', $from);
            $expense->whereDate('created_at', '<=', $to);

            $income->whereDate('created_at', '>=', $from);
            $income->whereDate('created_at', '<=', $to);
        }

        $incomingWithoutCityLedger = $income->clone()->sum('amount') - $this->getSumByModel($income, 7);
        $loss                      = $expense->clone()->sum('total') - $incomingWithoutCityLedger; // $income->clone()->sum('amount');
        $profit                    = $incomingWithoutCityLedger - $expense->clone()->sum('total');

        // return [
        //     'loss' =>   $expense->clone()->sum('total') - $incomingWithoutCityLedger,
        //     'profit' => $incomingWithoutCityLedger - $expense->clone()->sum('total'),
        // ];

        return [
            'expense'           => [
                'Cash'                   => $this->getSumByExpenseModel($expense, 1),
                'Card'                   => $this->getSumByExpenseModel($expense, 2),
                'Online'                 => $this->getSumByExpenseModel($expense, 3),
                'Bank'                   => $this->getSumByExpenseModel($expense, 4),
                'UPI'                    => $this->getSumByExpenseModel($expense, 5),
                'Cheque'                 => $this->getSumByExpenseModel($expense, 6),
                'OverallTotal'           => $expense->clone()->where('is_management', 0)->sum('total'),
                'ManagementOverallTotal' => $expense->clone()->where('is_management', 1)->sum('total'),
            ],
            'managementExpense' => [
                'Cash'                   => $this->getSumByExpenseModel($expense, 1, 1),
                'Card'                   => $this->getSumByExpenseModel($expense, 2, 1),
                'Online'                 => $this->getSumByExpenseModel($expense, 3, 1),
                'Bank'                   => $this->getSumByExpenseModel($expense, 4, 1),
                'UPI'                    => $this->getSumByExpenseModel($expense, 5, 1),
                'Cheque'                 => $this->getSumByExpenseModel($expense, 6, 1),
                'ManagementOverallTotal' => $expense->clone()->where('is_management', 1)->sum('total'),
            ],

            'income'            => [
                'Cash'         => $this->getSumByModel($income, 1),
                'Card'         => $this->getSumByModel($income, 2),
                'Online'       => $this->getSumByModel($income, 3),
                'Bank'         => $this->getSumByModel($income, 4),
                'UPI'          => $this->getSumByModel($income, 5),
                'Cheque'       => $this->getSumByModel($income, 6),
                'City_ledger'  => $this->getSumByModel($income, 7),
                'OverallTotal' => $incomingWithoutCityLedger,
            ],

            'profit'            => $profit != abs($profit) ? 0 : $profit,
            'loss'              => $loss != abs($loss) ? 0 : $loss,
        ];
    }

    public function getSumByModel($model, $id)
    {
        return $model->clone()->whereHas('paymentMode', fn($q) => $q->where('id', $id))->sum('amount');
    }

    public function getSumByExpenseModel($model, $id, $is_management = 0)
    {
        return $model->clone()->where('is_management', $is_management)->whereHas('paymentMode', fn($q) => $q->where('id', $id))->sum('total');
    }

    public function storeDocument($request, $model)
    {
        if ($request->hasFile('document')) {
            $file     = $request->file('document');
            $ext      = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $file->storeAs('public/documents/expense', $fileName);
            $model->document = $fileName;
            return $model->save();
        }
        return null;
    }
}
