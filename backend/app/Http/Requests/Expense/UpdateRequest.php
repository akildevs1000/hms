<?php

namespace App\Http\Requests\Expense;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'item' => 'nullable',
            'amount' => 'nullable',
            'payment_modes' => 'nullable',
            'qty' => 'nullable',
            'company_id' => 'nullable',
            'voucher' => 'nullable',
            'reference' => 'nullable',
            'total' => 'nullable',
            'description' => 'nullable',
            'document' => 'nullable',
        ];
    }
}
