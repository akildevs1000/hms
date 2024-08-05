<?php

namespace App\Http\Requests\SubCustomer;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            "dob" => "required",
            "first_name" => "required",
            "last_name" => "required",
            "address" => "required",
            "email" => "nullable",
            "customer_id" => "required",
        ];
    }
}
