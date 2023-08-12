<?php

namespace App\Http\Requests\Expense;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    use failedValidationWithName;
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
            'item' => 'required',
            'amount' => 'required',
            'payment_modes' => 'required',
            'qty' => 'required',
            'company_id' => 'required',
            'voucher' => 'required',

            'reference' => 'nullable',
            'total' => 'nullable',
            'description' => 'nullable',
            // 'document'      => 'nullable',
            // 'document1'      => 'nullable',
            // 'document2'      => 'nullable',
            // 'document3'      => 'nullable',
            'is_management' => 'nullable',
            'user' => 'nullable',

        ];
    }
}
