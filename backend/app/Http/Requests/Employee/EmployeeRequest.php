<?php

namespace App\Http\Requests\Employee;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'employee_id' => ['required'],
            'system_user_id' => ['required'],
            'first_name' => ['required', 'min:3', 'max:100'],
            'display_name' => ['required', 'min:3', 'max:10'],
            'title' => ['required'],
            'last_name' => ['required', 'min:3', 'max:100'],
            'email' => 'required|min:3|max:191|unique:users',
            'role_id' => ['required'],
            'profile_picture' => ['image', 'mimes:jpeg,png,jpg,svg', 'max:2048', 'sometimes', 'nullable'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:6', // must be at least 10 characters in length
                'max:25', // must be maximum 25 characters in length
                'regex:/[a-z]/', // must contain at least one lowercase letter
                'regex:/[A-Z]/', // must contain at least one uppercase letter
                'regex:/[0-9]/', // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],

        ];
    }

    public function messages()
    {
        return [
            'password.regex' => "You password must contain numeric, special(!@#$%) characters",
        ];
    }
}