<?php

namespace App\Http\Requests\ScheduleEmployee;

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
        return [
            'shift_id' => ["required"],
            'isOverTime' => ["nullable"],
            'shift_type_id' => ["required"],
            'employee_ids' => ["required", "array", "min:1"],
            'company_id' => ["required"],
        ];
    }
}
