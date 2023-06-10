<?php

namespace App\Http\Requests\RoomType;

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
            'name' => 'required',
            'price' => 'required|numeric',
            'room_only_price' => 'nullable',
            'Break_fast_price' => 'nullable',
            'Break_fast_with_dinner_price' => 'nullable',
            'Break_fast_with_lunch_price' => 'nullable',
            'lunch_with_dinner_price' => 'nullable',
            'full_board_price' => 'nullable',
            'max_person' => 'nullable',
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
