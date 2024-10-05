<?php

namespace App\Http\Controllers;

use App\Models\PostingPayment;
use Illuminate\Http\Request;

class PostingPaymentController extends Controller
{
    public function store(Request $request)
    {
        // return PostingPayment::first();
        // Validate incoming request
        $validatedData = $request->validate([
            'booking_id' => 'required|integer',
            'room_id' => 'required|integer',
            'sub_customer_id' => 'required|integer',
            'paid' => 'required|numeric',
            'balance' => 'required|numeric',
            'payment_mode' => 'nullable|string',
            'reference' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'after_discount_balance' => 'nullable|numeric',
        ]);


        $found = PostingPayment::where([
            "booking_id" => $validatedData["booking_id"],
            "room_id" => $validatedData["room_id"],
            "sub_customer_id" => $validatedData["sub_customer_id"],
        ])->first();


        try {
            if (!$found) {
                return PostingPayment::create($validatedData);
            }
            return $found->update([
                "paid" => $validatedData["paid"],
                "balance" => $validatedData["balance"],
                "payment_mode" => $validatedData["payment_mode"],
                "reference" => $validatedData["reference"],
                "discount" => $validatedData["discount"],
                "after_discount_balance" => $validatedData["discount"],
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
