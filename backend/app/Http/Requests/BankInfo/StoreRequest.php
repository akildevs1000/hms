<?php

namespace App\Http\Requests\BankInfo;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

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
            "bank_name" => "nullable|min:3|max:20",
            "account_no" => "nullable|min:6|max:20",
            "account_title" => "nullable|min:3|max:20",
            "iban" => "nullable|min:16|max:24",
            "address" => "nullable|min:1|max:24",
            "employee_id" => "required",
            "company_id" => "required",
        ];
    }
}
