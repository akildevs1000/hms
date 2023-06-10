<?php

namespace App\Http\Requests\RoomType;

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
            'name' => 'required',
            'price' => 'required|numeric',
            'company_id' => 'required',
            'adult' => 'required',
            'child' => 'required',
            'baby' => 'required',
            'holiday_price' => 'nullable',
            'weekday_price' => 'nullable',
            'weekend_price' => 'nullable',
        ];
    }
}
