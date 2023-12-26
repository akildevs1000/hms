<?php

namespace App\Http\Requests\Device;

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



            'serial_number' => ['required', Rule::unique('devices')->ignore($this->input('id'))->where(function ($query) {
                return $query->where('company_id', $this->input('company_id'));
            })],

            'name' => ['required', 'min:2', 'max:50'],
            'company_id' => ['required'],
            'room_id' => ['required'],


        ];
    }
}
