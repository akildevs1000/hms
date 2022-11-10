<?php

namespace App\Http\Requests\Role;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        $name = $this->name;
        $companyId = $this->company_id;
        return [
            'name' => [
                'required', 'min:4', 'max:100',
                Rule::unique('roles')->where(function ($query) use ($name, $companyId) {
                    return $query->where('name', $name)
                        ->where('company_id', $companyId);
                }),
            ],
            'company_id' => 'required',
        ];

    }
}
