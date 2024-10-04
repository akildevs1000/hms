<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpensePayment\ValidationRequest;
use App\Models\AdminExpense;
use App\Models\ExpensePayment;

class ExpensePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ExpensePayment::with("vendor")->where("admin_expense_id", request("admin_expense_id"))->get();
    }

    public function store(ValidationRequest $request)
    {
        $payment = ExpensePayment::create($request->validated());


        // $attachments = [];

        // foreach ($request->attachments as $aKey => $attachment) {

        //     $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $attachment['attachment']));
        //     $publicDirectory = public_path("attachments/payment/" . $payment->id);

        //     if (!file_exists($publicDirectory)) {
        //         mkdir($publicDirectory, 0777, true);
        //     }

        //     file_put_contents($publicDirectory . '/' . $attachment['name'] . ".png", $base64Image);

        //     $attachments[] = [
        //         "admin_expense_id" => $request->admin_expense_id,
        //         "attachment" => $attachment['name'] . ".png",
        //         "slug" => $attachment['name'],
        //         "model" => "payment",
        //     ];
        // }

        // Attachment::insert($attachments);

        AdminExpense::where("id", $request->admin_expense_id)->update(["status" => "Paid"]);

        return true;
    }

    public function update(ValidationRequest $request, ExpensePayment $expensePayment)
    {
        $expensePayment->update($request->validated());

        return $expensePayment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpensePayment  $expensePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpensePayment $expensePayment)
    {
        $expensePayment->delete();

        return response()->noContent();
    }
}
