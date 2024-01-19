<?php

namespace App\Http\Requests\HotelOrdersFood;

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

            'company_id' => 'required',
            'booking_id' => 'required',
            'room_id' => 'required',
            'food_item_id' => 'required',
            'qty' => 'required',


        ];
    }
}
