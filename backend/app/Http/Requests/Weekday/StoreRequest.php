<?php

namespace App\Http\Requests\Weekday;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

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
            'day' => 'required',
            'company_id' => 'required',
        ];
    }
}
