<?php

namespace App\Http\Requests\SubCustomer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            "image" => "nullable",
            "title" => "required",
            "contact_no" => "required",
            "whatsapp" => "nullable",
            "nationality" => "nullable",
            "dob" => "nullable",
            "first_name" => "nullable",
            "last_name" => "nullable",
            "address" => "nullable",
            "email" => "nullable",
            "customer_id" => "required",
        ];
    }
}
