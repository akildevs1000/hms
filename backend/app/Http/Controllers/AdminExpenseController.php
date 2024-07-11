<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminExpenseRequest\ValidationRequest;
use App\Models\AdminExpense;
use App\Models\AdminExpenseAttachment;
use App\Models\AdminExpenseItem;
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
    public function index()
    {
        return AdminExpense::with(
            [
                "vendor",
                "items",
                "attachments"
            ]
        )->paginate(request("per_page", 50));
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


        AdminExpenseAttachment::where("admin_expense_id", $AdminExpense->id)->whereIn("attachment", $existingAttachments)->delete();

        AdminExpenseAttachment::where("admin_expense_id", $AdminExpense->id)->delete();

        AdminExpenseAttachment::insert($attachments);

        AdminExpenseItem::where("admin_expense_id", $AdminExpense->id)->delete();

        AdminExpenseItem::insert($request->items);

        $AdminExpense->update($request->validated());

        return $AdminExpense;
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
}
