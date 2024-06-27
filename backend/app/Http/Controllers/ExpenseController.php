<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\ValidationRequest;
use App\Models\Attachment;
use App\Models\Expense;
use App\Models\ExpenseItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Expense::with(["vendor", "items", "attachments"])->paginate(request("per_page", 50));
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
            $expense = Expense::create($data);

            // Prepare the expense items
            $items = array_map(function ($item) use ($expense) {
                $item['expense_id'] = $expense->id;
                return $item;
            }, $request->items);


            $attachments = [];

            foreach ($request->attachments as $aKey => $attachment) {

                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $attachment['attachment']));
                $publicDirectory = public_path("attachments/expense/" . $expense->id);

                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory, 0777, true);
                }

                file_put_contents($publicDirectory . '/' . $attachment['name'] . ".png", $base64Image);

                $attachments[] = [
                    "expense_id" => $expense->id,
                    "attachment" => $attachment['name'] . ".png",
                    "slug" => $attachment['name'],
                    "model" => "expense",
                ];
            }


            Attachment::insert($attachments);

            // Insert the expense items
            ExpenseItem::insert($items);

            DB::commit();

            return true;

            return response()->json(['success' => true], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception or handle it as necessary
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Expense $expense)
    {
        $attachments = [];

        $existingAttachments = [];

        if ($request->attachments && count($request->attachments)) {

            foreach ($request->attachments as $aKey => $attachment) {

                if (array_key_exists("name", $attachment)) {
                    $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $attachment['attachment']));
                    $publicDirectory = public_path("attachments/expense/" . $expense->id);

                    if (!file_exists($publicDirectory)) {
                        mkdir($publicDirectory, 0777, true);
                    }

                    file_put_contents($publicDirectory . '/' . $attachment['name'] . ".png", $base64Image);


                    $attachments[] = [
                        "expense_id" => $expense->id,
                        "attachment" => $attachment['name'] . ".png",
                        "slug" => $attachment['name'],
                        "model" => "expense",
                    ];

                    $existingAttachments[] = $attachment['name'] . ".png";
                }
            }
        }


        // Attachment::where("expense_id", $expense->id)->whereIn("attachment", $existingAttachments)->delete();

        Attachment::where("expense_id", $expense->id)->delete();

        Attachment::insert($attachments);

        ExpenseItem::where("expense_id", $expense->id)->delete();

        ExpenseItem::insert($request->items);

        $expense->update($request->validated());

        return $expense;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        ExpenseItem::where("expense_id", $expense->id)->delete();

        return response()->noContent();
    }

    public function voucher($id)
    {
        $expense = Expense::with(["payment", "items", "vendor"])->find($id);

        $payment = $expense->payment;
        $payment->payee = request("payee");
        $vendor = $expense->vendor;
        $items = $expense->items;

        return Pdf::setPaper('a4', 'portrait')->loadView('pdf.payment.voucher', compact("expense", "payment", "vendor", "items"))->stream();
    }
}
