<?php

namespace App\Http\Requests\Followup;

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
            "description" => "required|min:5|max:500",
            "status" => "required",
            "quotation_id" => "required",

            "date" => "nullable",
            "user_id" => "required",
        ];
    }
}
