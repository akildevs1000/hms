<?php

namespace App\Http\Requests\Employee;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            // 'phone_number' => ['required', 'min:8', 'max:25'],
            // 'whatsapp_number' => ['required', 'min:8', 'max:25'],

            'local_address' => ['nullable', 'min:3', 'max:25'],
            'local_tel' => ['nullable', 'min:9', 'max:25'],
            'phone_number' => ['nullable', 'min:9', 'max:25'],
            'whatsapp_number' => ['nullable', 'min:9', 'max:25'],
            'phone_relative_number' => ['nullable', 'min:9', 'max:25'],
            'whatsapp_relative_number' => ['nullable', 'min:9', 'max:25'],

            'local_fax' => ['nullable', 'min:5', 'max:25'],
            'local_email' => ['nullable', 'min:3', 'max:25'],
            'local_country' => 'nullable', ['min:3', 'max:25'],
            'local_residence_no' => ['nullable', 'min:3', 'max:25'],
            'local_city' => ['nullable', 'min:3', 'max:25'],

            'home_address' => ['nullable', 'min:3', 'max:25'],
            'home_tel' => ['nullable', 'min:9', 'max:25'],
            'home_mobile' => ['nullable', 'min:9', 'max:25'],
            'home_fax' => ['nullable', 'min:5', 'max:25'],
            'home_city' => ['nullable', 'min:2', 'max:25'],
            'home_state' => ['nullable', 'min:2', 'max:25'],
            'home_country' => ['nullable', 'min:3', 'max:25'],
            'home_email' => ['nullable', 'min:3', 'max:25'],


            'company_id' => ['required'],
            'employee_id' => ['required'],
        ];
    }
}
