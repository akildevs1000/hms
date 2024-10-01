<?php

namespace App\Http\Requests\ExpensePayment;

use Illuminate\Foundation\Http\FormRequest;

class ValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'payment_number' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'attachment' => 'nullable',
            'note' => 'required|string|max:255',

            'payment_mode' => 'required',
            'payment_mode_ref' => 'nullable',
            'admin_expense_id' => 'required|exists:admin_expenses,id',


            'amount' => 'required',
            'discount' => 'required',
            'paid' => 'required',
            'balance' => 'required',

        ];
    }
}
