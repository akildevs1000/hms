<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminExpenseRequest\ValidationRequest;
use App\Models\AdminExpense;
use App\Models\AdminExpenseAttachment;
use App\Models\AdminExpenseItem;
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
        return AdminExpense::with(
            [
                "vendor",
                "items",
                "attachments"
            ]
        )
            ->where("is_admin_expense", request("is_admin_expense", AdminExpense::NonManagementExpense))
            ->paginate(request("per_page", 50));
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


            $attachments = [];

            foreach ($request->attachments as $aKey => $attachment) {

                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $attachment['attachment']));
                $publicDirectory = public_path("admin_expense_attachments/" . $expense->id);

                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory, 0777, true);
                }

                file_put_contents($publicDirectory . '/' . $attachment['name'] . ".png", $base64Image);

                $attachments[] = [
                    "admin_expense_id" => $expense->id,
                    "attachment" => $attachment['name'] . ".png",
                    "slug" => $attachment['name'],
                    "model" => "expense",
                ];
            }


            AdminExpenseAttachment::insert($attachments);

            // Insert the expense items
            AdminExpenseItem::insert($items);

            DB::commit();

            return true;

            return response()->json(['success' => true], 201);
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
        $attachments = [];

        $existingAttachments = [];

        if ($request->attachments && count($request->attachments)) {

            foreach ($request->attachments as $aKey => $attachment) {

                if (array_key_exists("name", $attachment)) {
                    $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $attachment['attachment']));
                    $publicDirectory = public_path("admin_expense_attachments/" . $AdminExpense->id);

                    if (!file_exists($publicDirectory)) {
                        mkdir($publicDirectory, 0777, true);
                    }

                    file_put_contents($publicDirectory . '/' . $attachment['name'] . ".png", $base64Image);


                    $attachments[] = [
                        "admin_expense_id" => $AdminExpense->id,
                        "attachment" => $attachment['name'] . ".png",
                        "slug" => $attachment['name'],
                        "model" => "expense",
                    ];

                    $existingAttachments[] = $attachment['name'] . ".png";
                }
            }
        }

        DB::beginTransaction();

        try {
            // AdminExpenseAttachment::where("admin_expense_id", $AdminExpense->id)->whereIn("attachment", $existingAttachments)->delete();

            // AdminExpenseAttachment::where("admin_expense_id", $AdminExpense->id)->delete();

            // AdminExpenseAttachment::insert($attachments);

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


        $is_admin_expense = request("is_admin_expense", 0);

        $modes = PaymentMode::pluck("name")->map(fn($mode) => str_replace(' ', '', $mode))->toArray();

        $items = AdminExpenseItem::whereHas("expense", function ($q) use ($is_admin_expense, $searchKey, $fromDate, $toDate) {
            $q->where("is_admin_expense", $is_admin_expense);

            $q->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, date('Y-m-d', strtotime($toDate . ' +1 day'))]);
            });

            $q->whereHas("vendor", function ($childQuery) use ($searchKey) {

                $childQuery->where('first_name', 'like', "%{$searchKey}%")
                    ->orWhere('last_name', 'like', "%{$searchKey}%");
            });
        })
            ->with("expense")->get()->map(function ($item) use ($modes) {
                // Initialize all payment modes to 0
                foreach ($modes as $mode) {
                    $item[$mode] = 0;
                }

                // Set the appropriate payment mode if it exists
                if ($item->expense->payment && in_array($item->expense->payment->payment_mode, $modes)) {
                    $item[$item->expense->payment->payment_mode] = $item->expense->total;
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

        $stats = collect($expenseModes)->mapWithKeys(function ($mode, $key) use ($is_admin_expense, $searchKey, $fromDate, $toDate) {
            return [
                $key => AdminExpense::whereHas('payment', function ($q) use ($mode, $is_admin_expense, $searchKey, $fromDate, $toDate) {
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

        $stats['total'] = AdminExpense::whereHas('payment', function ($q) use ($is_admin_expense) {
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
}
