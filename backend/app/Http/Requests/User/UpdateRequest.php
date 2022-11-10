<?php

namespace App\Http\Requests\User;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => 'required|min:3|max:100',
            'email' => 'required|min:3|max:191',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:6',             // must be at least 10 characters in length
                'max:25',             // must be maximum 25 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'role_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            "role_id.required" => "The role field is required."
        ];
    }
}
