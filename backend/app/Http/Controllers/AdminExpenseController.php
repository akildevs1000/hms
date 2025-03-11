<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminExpenseRequest\ValidationRequest;
use App\Models\AdminExpense;
use App\Models\AdminExpenseAttachment;
use App\Models\AdminExpenseItem;
use App\Models\Company;
use App\Models\PaymentMode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLastThreeRecords()
    {
        return AdminExpense::where("is_admin_expense", request("is_admin_expense", AdminExpense::NonManagementExpense))
            ->where("vendor_id", request("vendor_id", 0))
            ->latest()
            ->take(3)
            ->get();
    }

    public function index()
    {
        return $this->getData()->paginate(request("perPage", 50));
    }

    public function print()
    {
        $data = $this->getData()->get();
        $pageTitle = "Expense Report";

        $company = Company::with("user:id,company_id,email")->select("id", "name", "user_id", "location", "logo")->find(request("company_id", 3));


        return Pdf::loadView("expense.index", compact("pageTitle", "company", "data"))
            ->setPaper('a4', 'landscape')
            ->stream();
    }

    public function download()
    {
        $data = $this->getData()->get();
        $pageTitle = "Expense Report";

        $company = Company::with("user:id,company_id,email")->select("id", "name", "user_id", "location", "logo")->find(request("company_id", 3));


        return Pdf::loadView("expense.index", compact("pageTitle", "company", "data"))
            ->setPaper('a4', 'landscape')
            ->download();
    }

    function getData()
    {
        $fromDate = request()->input('from', null);
        $toDate =   request()->input('to', null);
        $vendor_category_id = request()->input('vendor_category_id', 0);
        $vendor_id = request()->input('vendor_id', 0);


        if ($vendor_category_id == 0) $vendor_id = null;
        if ($vendor_id == 0) $vendor_category_id = null;

        return AdminExpense::with(
            [
                "vendor",
                "items",
                "attachments"
            ]
        )

            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('bill_date', [$fromDate, $toDate]);
            })

            ->when($vendor_category_id, function ($query) use ($vendor_category_id) {
                $query->whereHas('vendor', function ($q) use ($vendor_category_id) {
                    $q->where('vendor_category_id', $vendor_category_id);
                });
            })

            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('vendor_id', $vendor_id);
            })
            ->when(request()->has('is_admin_expense'), function ($q) {
                $q->where('is_admin_expense', request("is_admin_expense"));
            })
            ->whereHas("vendor")
            ->where("company_id", request("company_id"))
            ->orderBy("id", "desc");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();

            // Create the main expense record
            $expense = AdminExpense::create($data);

            // Prepare the expense items
            $items = array_map(function ($item) use ($expense) {
                $item['admin_expense_id'] = $expense->id;
                return $item;
            }, $request->items);

            // Insert the expense items
            AdminExpenseItem::insert($items);

            DB::commit();


            return response()->json(['success' => true, "record" => $expense], 201);
        } catch (\Exception $e) {


            DB::rollBack();
            // Log the exception or handle it as necessary
            return $e->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdminExpense  $adminExpense
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, AdminExpense $AdminExpense)
    {
        try {
            DB::beginTransaction();

            AdminExpenseItem::where("admin_expense_id", $AdminExpense->id)->delete();

            AdminExpenseItem::insert($request->items);

            $AdminExpense->update($request->validated());

            DB::commit();

            return $AdminExpense;
        } catch (\Exception $e) {

            DB::rollBack();

            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdminExpense  $adminExpense
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdminExpense $AdminExpense)
    {
        $AdminExpense->delete();

        AdminExpenseItem::where("admin_expense_id", $AdminExpense->id)->delete();

        return response()->noContent();
    }

    public function voucher($id)
    {
        try {
            $expense = AdminExpense::with(["payment", "items", "vendor"])->find($id);
            $payment = $expense->payment;
            $payment->payee = request("payee");
            $vendor = $expense->vendor;
            $items = $expense->items;
            return Pdf::setPaper('a4', 'portrait')->loadView('pdf.payment.voucher', compact("expense", "payment", "vendor", "items"))->stream();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function expenseCount()
    {
        $searchKey = request('search', null);
        $fromDate = request('from_date', null);
        $toDate = request('to_date', null);
        $company_id = request('company_id', null);

        $is_admin_expense = request("is_admin_expense", 0);

        $modes = PaymentMode::pluck("name")->map(fn($mode) => str_replace(' ', '', $mode))->toArray();

        $items = AdminExpenseItem::whereHas("expense", function ($q) use ($company_id, $is_admin_expense, $searchKey, $fromDate, $toDate) {
            $q->where("is_admin_expense", $is_admin_expense);
            $q->where("company_id", $company_id);
            $q->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, date('Y-m-d', strtotime($toDate . ' +1 day'))]);
            });

            $q->whereHas("vendor", function ($childQuery) use ($searchKey) {

                $childQuery->where('first_name', 'like', "%{$searchKey}%")
                    ->orWhere('last_name', 'like', "%{$searchKey}%");
            });
        })
            ->with("expense")->get()->map(function ($item) use ($modes, $company_id) {


                // Initialize all payment modes to 0
                foreach ($modes as $mode) {
                    $item[$mode] = 0;
                }

                // Set the appropriate payment mode if it exists
                if ($item->expense->company_id == $company_id && $item->expense->payment && in_array($item->expense->payment->payment_mode, $modes)) {
                    $item[$item->expense->payment->payment_mode] = $item->expense->payment_sum_paid;
                }

                return $item;
            });


        $expenseModes = [
            'Cash' => AdminExpense::CASH,
            'Card' => AdminExpense::CARD,
            'Online' => AdminExpense::ONLINE,
            'Bank' => AdminExpense::BANK,
            'UPI' => AdminExpense::UPI,
            'Cheque' => AdminExpense::CHEQUE,
            'CityLedger' => AdminExpense::CITYLEDGER,
        ];

        $stats = collect($expenseModes)->mapWithKeys(function ($mode, $key) use ($is_admin_expense, $searchKey, $fromDate, $toDate, $company_id) {
            return [
                $key => AdminExpense::where("company_id", $company_id)->whereHas('payment', function ($q) use ($mode, $is_admin_expense, $searchKey, $fromDate, $toDate) {
                    $q->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('created_at', [$fromDate, date('Y-m-d', strtotime($toDate . ' +1 day'))]);
                    });
                    $q->where('payment_mode', $mode)
                        ->where('is_admin_expense', $is_admin_expense);
                })
                    ->whereHas("vendor", function ($childQuery) use ($searchKey) {

                        $childQuery->where('first_name', 'like', "%{$searchKey}%")
                            ->orWhere('last_name', 'like', "%{$searchKey}%");
                    })

                    ->sum('total'),
            ];
        })->toArray();

        $stats['total'] = AdminExpense::where("company_id", $company_id)->whereHas('payment', function ($q) use ($is_admin_expense) {
            $q->where('payment_mode', '!=', AdminExpense::CITYLEDGER)
                ->where('is_admin_expense', $is_admin_expense);
        })->sum('total');


        return [
            "data" => $items,
            "stats" => $stats
        ];
    }

    public function lastAdminExpenseNumber()
    {
        // Get the last expense ID for the given company_id
        $lastId = AdminExpense::where("company_id", request("company_id", 0))
            ->max("id");

        // If no ID is found, set it to 0
        $lastId = $lastId + 1 ?? 0;

        // Return formatted ID with leading zeros if necessary
        return sprintf('%04d', $lastId);
    }

    public function FileUploads(Request $request, $modelId = 0)
    {
        try {
            $request->validate([
                'files.*' => 'required|max:2048', // Example validation
            ]);

            $attachments = [];

            $uploadedFiles = $request->file('files'); // Get all uploaded files

            foreach ($uploadedFiles as $key =>  $file) {

                // Save the file with the original extension
                $extension = $file->getClientOriginalExtension();

                // Generate a unique file name
                $uniqueFileName = $key . uniqid();

                $uniqueFileNameWithExt = $uniqueFileName . '.' . $extension;

                $publicDirectory = public_path("expense-uploads/" . $modelId);

                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory, 0777, true);
                }

                // Store the file in the public directory under 'uploads' folder
                $file->move($publicDirectory, $uniqueFileNameWithExt);

                $attachments[] = [
                    "admin_expense_id" => $modelId,
                    "attachment" => $uniqueFileNameWithExt,
                    "slug" => $uniqueFileName,
                    "model" => "expense",
                ];
            }

            AdminExpenseAttachment::where("admin_expense_id", $modelId)->delete();

            AdminExpenseAttachment::insert($attachments);

            DB::commit();
            return response()->json(['message' => 'Files uploaded successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to upload file'], 500);
        }
    }
}
