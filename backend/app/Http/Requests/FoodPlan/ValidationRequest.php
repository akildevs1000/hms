<?php

namespace App\Http\Requests\FoodPlan;

use Illuminate\Foundation\Http\FormRequest;

class ValidationRequest extends FormRequest
{
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
            "title" => "required|min:2|max:255",
            "description" => "required|min:2|max:255",
            "no_of_pax" => "required|integer",
            "unit_price" => "required|integer",
        ];
    }
}
