<?php

namespace App\Http\Requests\Room;

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
            'room_no' => 'required',
            'room_type_id' => 'required',
            'company_id' => 'required',
            'status' => 'required',
            'floor_no' => 'required',
            'user_id' => 'nullable',

        ];
    }
}
