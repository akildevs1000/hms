<?php

namespace App\Http\Requests\TimeTable;

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

            'on_duty_time' => 'required',
            'off_duty_time' => 'required',
            'late_time' => 'required',
            'early_time' => 'required',
            'beginning_in' => 'required',
            'ending_in' => 'required',
            'beginning_out' => 'required',
            'ending_out' => 'required',
            'count_as_workday' => 'required',
            'count_as_minute' => 'required',
            'company_id' => 'nullable',
            'break_time_start' => 'required',
            'break_time_end' => 'required',
            'absent_min_in' => 'required',
            'absent_min_out' => 'required',
            'shift_id' => 'required',
        ];
    }
}
