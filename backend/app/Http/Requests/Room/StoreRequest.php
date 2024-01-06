<?php

namespace App\Http\Requests\Room;

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
            'room_no' => 'required',
            'room_type_id' => 'required',
            'floor_no' => 'nullable',
            'user_id' => 'nullable',
            'online_available' => 'required',


        ];
    }
}
