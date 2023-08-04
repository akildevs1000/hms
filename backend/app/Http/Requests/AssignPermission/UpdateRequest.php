<?php

namespace App\Http\Requests\AssignPermission;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use failedValidationWithName; // gives response when validation failed

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'role_id' => 'required',
            'permission_ids' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'role_id.required' => 'The role field is required',
            'permission_ids.min' => 'Atleast 1 permission must be selected',
        ];
    }

}
