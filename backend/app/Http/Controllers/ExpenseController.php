<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Models\ExpensesDocuments;
use App\Models\Expense;
use App\Models\ExpensesCategories;
use App\Models\Payment;
use Carbon\Carbon;
use Database\Seeders\ExpenseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $model->with(['category', 'expenese_docuemnts']);
        $model->where('company_id', $request->company_id);
        // $model->where(function ($q) use ($request) {
        //     $q->where('voucher', 0);
        //     $q->orWhere('item', 'ILIKE', "%$request->search%");
        // });
        if ($request->filled('search')) {
            $model->Where('item', 'ILIKE', "%$request->search%");
        }

        if ($request->filled('from') && $request->filled('to')) {
            $model->whereDate('created_at', '>=', $request->from . ' 00:00:00');
            $model->whereDate('created_at', '<=', $request->to . ' 23:59:00');
            $model->orderBy("created_at", 'asc');
        } else {
            $model->orderBy("created_at", 'desc');
        }
        if ($request->filled('category_id')) {
            $model->where('category_id',   $request->category_id);
        }
        if ($request->filled('is_management')) {
            $model->where('is_management', $request->is_management);
        } else {
            //  $model->where('is_management', 0);
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
            $to = $request->to_date;
            $model->whereDate('created_at', '>=', $from);
            $model->whereDate('created_at', '<=', $to);
        }

        if ($request->is_account && $request->has('is_account') && $request->filled('is_account')) {
            return $model->get();
        }

        return $model->paginate($request->per_page ?? 50);
    }

    public function search(Request $request, $key)
    {
        $model = Expense::query();
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

            // return $request->validated();

            $record = Expense::create($request->validated());

            if ($record) {
                //$expense = Expense::where('id', $record->id);
                // $this->updateDocument($request, $expense, 'document', $record->id);
                // $this->updateDocument($request, $expense, 'document1', $record->id);
                // $this->updateDocument($request, $expense, 'document2', $record->id);
                // $this->updateDocument($request, $expense, 'document3', $record->id);

                $this->storeDocuments($request, $record->id);


                return $this->response($this->name . ' Successfully created.', $record, true);
            } else {
                return $this->response($this->name . ' cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function getStaticstics(Request $request)
    {



        $categoriesWithExpenses = Expense::select('category_id', DB::raw('COALESCE(SUM(total), 0) as total'))
            ->where('company_id', $request->company_id)

            ->when($request->filled('from') && $request->filled('to'), function ($query) use ($request) {
                // $from = Carbon::parse($request->from)->startOfDay();
                // $to = Carbon::parse($request->to)->endOfDay();
                // $query->whereBetween('created_at', [$from, $to]);

                $query->whereDate('created_at', '>=', $request->from . ' 00:00:00');
                $query->whereDate('created_at', '<=', $request->to . ' 23:59:00');
            })
            ->when($request->filled('is_management'), function ($query) use ($request) {
                $query->where('is_management', $request->is_management);
            });

        if ($request->filled('category_id')) {
            $categoriesWithExpenses->where('category_id',   $request->category_id);
        }
        if ($request->filled('is_management')) {
            $categoriesWithExpenses->where('is_management', $request->is_management);
        }
        $categoriesWithExpenses = $categoriesWithExpenses->groupBy('category_id')
            ->get();
        return $categoriesWithExpenses;

        // $categoriesWithExpenses = ExpensesCategories::leftJoin('expenses', 'expenses_categories.id', '=', 'expenses.category_id');
        // $categoriesWithExpenses =  $categoriesWithExpenses->select('expenses_categories.name', 'expenses_categories.id', DB::raw('COALESCE(SUM(expenses.amount), 0) as total'));
        // if ($request->filled('from') && $request->filled('to')) {
        //     $categoriesWithExpenses->whereDate('expenses.created_at', '>=', $request->from . ' 00:00:00');
        //     $categoriesWithExpenses->whereDate('expenses.created_at', '<=', $request->to . ' 23:59:00');
        // }
        // $categoriesWithExpenses =  $categoriesWithExpenses->groupBy('expenses_categories.id', 'expenses_categories.name');
        // $categoriesWithExpenses =  $categoriesWithExpenses->get();



        return $categoriesWithExpenses;
    }
    public function storeDocuments($request, $expenses_id)
    {
        $arr = [];
        if ($request->items)
            foreach ($request->items as $item) {
                $arr[] = [
                    "title" => $item["title"],
                    "file_name" => $this->saveFile($item["file"], $request->company_id),
                    "expenses_id" => $expenses_id,
                    "company_id" => $request->company_id,
                ];
            }
        ExpensesDocuments::insert($arr);

        return $arr;
    }
    public function saveFile($file, $id)
    {

        $ext = $file->getClientOriginalExtension();
        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $file->storeAs('public/expenses_documents/' . $id . '/', $fileName);

        return $fileName;
    }
    // public function storeDocument($request, $model, $docFileName, $id)
    // {
    //     if ($request->hasFile($docFileName)) {

    //         $file = $request->file($docFileName);
    //         $ext = $file->getClientOriginalExtension();
    //         // $fileName = time() . '.' . $ext;
    //         $fileName = time() . '_' . uniqid() . '.' . $ext;
    //         $file->storeAs('public/documents/expense', $fileName);
    //         $model->$docFileName = $fileName;
    //         $model->save();

    //         //$model->update([$docFileName => $fileName]);
    //         return $fileName;
    //     }
    //     return null;
    // }
    public function updateDocument($request, $model, $docFileName, $id)
    {
        if ($request->hasFile($docFileName)) {

            $file = $request->file($docFileName);
            $ext = $file->getClientOriginalExtension();
            // $fileName = time() . '.' . $ext;
            $fileName = time() . '_' . uniqid() . '.' . $ext;
            $file->storeAs('public/documents/expense', $fileName);
            // $model->$docFileName = $fileName;
            // $model->save();

            $model->update([$docFileName => $fileName]);
            return $fileName;
        }
        return null;
    }

    public function update(UpdateRequest $request, Expense $expense)
    {

        try {
            $record = $expense->update($request->validated());
            if ($record) {


                $this->storeDocuments($request, $request->id);

                //$this->storeDocument($request, $expense);

                //return $expense->first();
                // $expense = Expense::where('id', $request->id);
                // $this->updateDocument($request, $expense, 'document', $request->id);

                // $this->updateDocument($request, $expense, 'document1', $request->id);
                // $this->updateDocument($request, $expense, 'document2', $request->id);
                // $this->updateDocument($request, $expense, 'document3', $request->id);

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

        $incomingWithoutCityLedger = $income->clone()->sum('amount') - $this->getSumByModel($income, 7);
        $loss = $expense->clone()->sum('total') - $incomingWithoutCityLedger;
        $profit = $incomingWithoutCityLedger - $expense->clone()->sum('total');

        return [
            'expense' => [
                'Cash' => $this->getSumByExpenseModel($expense, 1),
                'Card' => $this->getSumByExpenseModel($expense, 2),
                'Online' => $this->getSumByExpenseModel($expense, 3),
                'Bank' => $this->getSumByExpenseModel($expense, 4),
                'UPI' => $this->getSumByExpenseModel($expense, 5),
                'Cheque' => $this->getSumByExpenseModel($expense, 6),
                'OverallTotal' => $expense->clone()->where('is_management', 0)->sum('total'),
                'ManagementOverallTotal' => $expense->clone()->where('is_management', 1)->sum('total'),
            ],
            'managementExpense' => [
                'Cash' => $this->getSumByExpenseModel($expense, 1, 1),
                'Card' => $this->getSumByExpenseModel($expense, 2, 1),
                'Online' => $this->getSumByExpenseModel($expense, 3, 1),
                'Bank' => $this->getSumByExpenseModel($expense, 4, 1),
                'UPI' => $this->getSumByExpenseModel($expense, 5, 1),
                'Cheque' => $this->getSumByExpenseModel($expense, 6, 1),
                'ManagementOverallTotal' => $expense->clone()->where('is_management', 1)->sum('total'),
            ],

            'income' => [
                'Cash' => $this->getSumByModel($income, 1),
                'Card' => $this->getSumByModel($income, 2),
                'Online' => $this->getSumByModel($income, 3),
                'Bank' => $this->getSumByModel($income, 4),
                'UPI' => $this->getSumByModel($income, 5),
                'Cheque' => $this->getSumByModel($income, 6),
                'City_ledger' => $this->getSumByModel($income, 7),
                'OverallTotal' => $incomingWithoutCityLedger,
            ],

            'profit' => $profit != abs($profit) ? 0 : $profit,
            'loss' => $loss != abs($loss) ? 0 : $loss,
        ];
    }

    public function getSumByModel($model, $id)
    {
        return $model->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', $id))->sum('amount');
    }

    public function getSumByExpenseModel($model, $id, $is_management = 0)
    {
        return $model->clone()->where('is_management', $is_management)->whereHas('paymentMode', fn ($q) => $q->where('id', $id))->sum('total');
    }

    public function expensesDocuments($expenses_id)
    {

        return ExpensesDocuments::where('expenses_id', $expenses_id)->get();
    }

    public function expensesDocumentsDelete(Request $request, $expenses_document_id)
    {


        $record = ExpensesDocuments::where('company_id', $request->company_id)->whereId($expenses_document_id)->delete();
        if ($record) {
            return $this->response($this->name . ' Document is  successfully deleted.', $record, true);
        } else {
            return $this->response($this->name . ' Document cannot delete.', null, false);
        }
    }
}
