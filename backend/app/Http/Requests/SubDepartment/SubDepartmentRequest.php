<?php

namespace App\Http\Requests\SubDepartment;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class SubDepartmentRequest extends FormRequest
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
            'name' => 'required|min:2|max:20|unique:sub_departments',
            'department_id' => 'required',
            'company_id' => 'required',
        ];
    }
}
