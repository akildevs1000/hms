<?php

namespace App\Http\Requests\Quotation;

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
            'ref_no' => 'nullable',
            'customer_id' => 'required|exists:customers,id',
            'book_date' => 'required|date',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date',
            'sub_total' => 'required|max:255',
            'discount' => 'required|max:255',
            'tax' => 'required|max:255',
            'total' => 'required|max:255',
            'bank_details' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'status' => 'nullable|string|max:255',
        ];
    }
}
