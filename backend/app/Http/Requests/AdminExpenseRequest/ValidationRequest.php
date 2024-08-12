<?php

namespace App\Http\Requests\AdminExpenseRequest;

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
            'bill_number' => 'required|string|max:255',
            'bill_date' => 'required|date',
            'attachment' => 'nullable',
            'sub_total' => 'required|max:255',
            'discount' => 'required|max:255',
            'total' => 'required|max:255',
            'status' => 'nullable|string|max:255',
            'is_admin_expense' => "nullable",
        ];
    }
}
