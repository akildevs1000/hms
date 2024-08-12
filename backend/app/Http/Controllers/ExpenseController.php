<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Models\AdminExpense;
use App\Models\ExpensesDocuments;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\ExpensesCategories;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Vendors;
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
        $model->with(['category', 'expenese_docuemnts', 'vendor']);
        $model->where('company_id', $request->company_id);

        // $model->where(function ($q) use ($request) {
        //     $q->where('voucher', 0);
        //     $q->orWhere('item', 'ILIKE', "%$request->search%");
        // });
        if ($request->filled('search')) {
            $model->Where(function ($q) use ($request) {
                $q->Where('item', 'ILIKE', "%$request->search%");
                $q->orWhere('voucher', 'ILIKE', "%$request->search%");
            });
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
        if ($request->filled('vendor_id')) {
            $model->where('vendor_id',   $request->vendor_id);
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


            $data = $request->validated();


            if ($request->vendor_id == 0 && $request->filled('vendor_new_name')) {
                $vendors = Vendors::where("company_id", $request->company_id)->where("name", $request->vendor_new_name)->get();
                if ($vendors->count() == 0) {
                    $vendor_data['name'] = $request->vendor_new_name;
                    $vendor_data['address'] = $request->vendor_new_address;
                    $vendor_data['contact_numbers'] = $request->vendor_new_contact;
                    $vendor_data['company_id'] = $request->company_id;

                    $vendor = Vendors::create($vendor_data);
                    $data["vendor_id"] = $vendor->id;
                } else {

                    $data["vendor_id"] = $vendors[0]->id;
                }
            }


            // return $request->validated();

            $record = Expense::create($data);

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


        if ($request->filled('search')) {
            $categoriesWithExpenses->Where(function ($q) use ($request) {
                $q->Where('item', 'ILIKE', "%$request->search%");
                $q->orWhere('voucher', 'ILIKE', "%$request->search%");
            });
        }

        if ($request->filled('category_id')) {
            $categoriesWithExpenses->where('category_id',   $request->category_id);
        }
        if ($request->filled('vendor_id')) {
            $categoriesWithExpenses->where('vendor_id',   $request->vendor_id);
        }

        if ($request->filled('is_management')) {
            $categoriesWithExpenses->where('is_management', $request->is_management);
        } else {
            //  $model->where('is_management', 0);
        }

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


                if (isset($item["title"]) && isset($item["file"])) {
                    if ($item["title"] && $item["file"]) {
                        $arr  = [
                            "title" => $item["title"],
                            "file_name" => $this->saveFile($item["file"], $request->company_id),
                            "expenses_id" => $expenses_id,
                            "company_id" => $request->company_id,
                        ];
                        ExpensesDocuments::insert($arr);
                    }


                    //  ExpensesDocuments::insert($arr);
                }
                // if (isset($item["title"]) &&   isset($item["file"])) {
                //     if ($item["title"] != null) {
                //         $arr[] = [
                //             "title" => $item["title"],
                //             "file_name" => $this->saveFile($item["file"], $request->company_id),
                //             "expenses_id" => $expenses_id,
                //             "company_id" => $request->company_id,
                //         ];
                //     }


                //     //  ExpensesDocuments::insert($arr);
                // }


            }


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


            $data = $request->validated();

            if ($request->vendor_id == 0 && $request->filled('vendor_new_name')) {
                $vendors = Vendors::where("company_id", $request->company_id)->where("name", $request->vendor_new_name)->get();
                if ($vendors->count() == 0) {
                    $vendor_data['name'] = $request->vendor_new_name;
                    $vendor_data['address'] = $request->vendor_new_address;
                    $vendor_data['contact_numbers'] = $request->vendor_new_contact;
                    $vendor_data['company_id'] = $request->company_id;

                    $vendor = Vendors::create($vendor_data);
                    $data["vendor_id"] = $vendor->id;
                } else {

                    $data["vendor_id"] = $vendors[0]->id;
                }
            }

            $record = $expense->update($data);
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
        // $expense = AdminExpense::where('company_id', $request->company_id)
        //     ->orderByDesc("id");

        // $income = Payment::query()
        //     ->where('company_id', $request->company_id)
        //     ->whereHas('booking', function ($q) {
        //         $q->where('booking_status', '!=', -1);
        //     })
        //     ->orderByDesc("id");

        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $from = $request->from_date;
        //     $to = $request->to_date;
        //     $expense->whereDate('created_at', '>=', $from);
        //     $expense->whereDate('created_at', '<=', $to);

        //     $income->whereDate('created_at', '>=', $from);
        //     $income->whereDate('created_at', '<=', $to);
        // }

        // $incomingWithoutCityLedger = $income->clone()->sum('amount') - $this->getSumByModel($income, 7);
        // $loss = $expense->clone()->sum('total') - $incomingWithoutCityLedger;
        // $profit = $incomingWithoutCityLedger - $expense->clone()->sum('total');



        $loss = 0;
        $profit = 0;

        return [
            'expense' => $this->getExpenses(AdminExpense::NonManagementExpense),
            'managementExpense' => $this->getExpenses(AdminExpense::ManagementExpense),

            'income' => $this->getExpenses(AdminExpense::ManagementExpense),

            'profit' => $profit != abs($profit) ? 0 : $profit,
            'loss' => $loss != abs($loss) ? 0 : $loss,
        ];
    }

    public function getExpenses($ExpenseCondition)
    {
        $expenseModes = [
            'Cash' => AdminExpense::CASH,
            'Card' => AdminExpense::CARD,
            'Online' => AdminExpense::ONLINE,
            'Bank' => AdminExpense::BANK,
            'UPI' => AdminExpense::UPI,
            'Cheque' => AdminExpense::CHEQUE,
            'CityLedger' => AdminExpense::CITYLEDGER,
        ];

        $expenses = collect($expenseModes)->mapWithKeys(function ($mode, $key) use ($ExpenseCondition) {
            return [
                $key => AdminExpense::whereHas('payment', function ($q) use ($mode, $ExpenseCondition) {
                    $q->where('payment_mode', $mode)
                        ->where('is_admin_expense', $ExpenseCondition);
                })->sum('total'),
            ];
        })->toArray();

        $expenses['WithOutCityLedger'] = AdminExpense::whereHas('payment', function ($q) use ($ExpenseCondition) {
            $q->where('payment_mode', '!=', AdminExpense::CITYLEDGER)
                ->where('is_admin_expense', $ExpenseCondition);
        })->sum('total');

        $expenses['Total'] = AdminExpense::whereHas('payment', function ($q) use ($ExpenseCondition) {
            $q->where('is_admin_expense', $ExpenseCondition);
        })->sum('total');

        return $expenses;
    }

    public function getSumByModel($model, $id)
    {
        return $model->clone()->whereHas('paymentMode', fn($q) => $q->where('id', $id))->sum('amount');
    }

    public function getSumByExpenseModel($model, $id, $is_management = 0)
    {
        return $model->clone()->where('is_management', $is_management)->whereHas('paymentMode', fn($q) => $q->where('id', $id))->sum('total');
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
