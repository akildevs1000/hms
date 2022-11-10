<?php

namespace App\Http\Requests\Passport;

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
            "passport_no" => "nullable|min:2|max:20",
            "place_of_issues" => "nullable|min:1|max:20",
            "country" => "nullable|min:1|max:20",
            "note" => "nullable",
            "issue_date" => "nullable",
            "expiry_date" => "nullable",
            "employee_id" => "required",
            "company_id" => "required",
        ];
    }
}
