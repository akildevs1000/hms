<?php

namespace App\Http\Requests\Employee;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateContact extends FormRequest
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
            'phone_number' => ['nullable', 'min:9', 'max:25'],
            'whatsapp_number' => ['nullable', 'min:9', 'max:25'],
            'phone_relative_number' => ['nullable', 'min:9', 'max:25'],
            'relation' => ['min:3', 'nullable', 'max:100'],
            'local_email' => ['min:3', 'nullable', 'max:100'],
            'local_address' => ['min:3', 'nullable', 'max:100'],
            'local_country' => ['min:3', 'nullable', 'max:100'],
            'local_city' => ['min:3', 'nullable', 'max:100'],
        ];
    }

}