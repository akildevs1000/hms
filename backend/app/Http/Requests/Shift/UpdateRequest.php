<?php

namespace App\Http\Requests\Shift;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => ['required', Rule::unique('shifts')->ignore($this->shift)],
            'overtime_interval' => ["required"],
            'shift_type_id' => ["required"],
            'company_id' => ["required"],
            'working_hours' => ['nullable'],
            'days' => ['nullable'],
            'on_duty_time' => 'nullable',
            'off_duty_time' => 'nullable',
            'late_time' => 'nullable',
            'early_time' => 'nullable',
            'beginning_in' => 'nullable',
            'ending_in' => 'nullable',
            'beginning_out' => 'nullable',
            'ending_out' => 'nullable',
            'absent_min_in' => 'nullable',
            'absent_min_out' => 'nullable',
        ];
    }
}