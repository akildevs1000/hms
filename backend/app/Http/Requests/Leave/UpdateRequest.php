<?php

namespace App\Http\Requests\Leave;

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
            'title' => 'required|min:4|max:30',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'type' => 'required',
            'is_approved' => 'required',
            'description' => 'required',

            'user_id' => 'required',
            'supervisor' => 'required',
            'approved_by' => 'nullable',

            'company_id' => 'required',
            'branch_id' => 'required',
        ];
    }
}
