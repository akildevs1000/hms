<?php

namespace App\Http\Requests\Company;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        $arr = [
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

        if (!$this->is_master) {
            $arr['current_password'] = 'required';
        }
        return $arr;
    }

    public function messages()
    {
        return [
            'password.regex' => "You password must contain numeric, special(!@#$%) characters",
        ];
    }
}