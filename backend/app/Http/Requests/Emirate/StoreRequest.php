<?php

namespace App\Http\Requests\Emirate;

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
            "emirate_id" => "nullable|min:2|max:20",
            "name" => "nullable|min:1|max:20",
            "gender" => "nullable|min:1|max:20",
            "date_of_birth" => "nullable",
            "nationality" => "nullable",
            "issue" => "nullable",
            "expiry" => "nullable",

            "company_id" => "required",
            "employee_id" => "required",
        ];
    }
}
