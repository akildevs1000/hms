<?php

namespace App\Http\Requests\HotelFoodItems;

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
            'company_id' => 'required',
            'name' => 'required',
            'category_id' => 'required',
            'timing_id' => 'required',
            'ingredients' => 'nullable',
            'is_non_veg' => 'required',
            'amount' => 'required',
            'description' => 'nullable',
            'status' => 'required',
        ];
    }
}
