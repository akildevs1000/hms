<?php

namespace App\Http\Requests\Booking;

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

        $arr = [
            'room_type'       => 'required',
            'type'            => 'required',
            'room_id'         => 'required',
            'check_in'        => 'required',
            'check_out'       => 'required',
            'company_id'      => 'required',

            'first_name'      => 'required',
            'contact_no'      => 'required|max:15|min:8',
            'id_card_type_id' => 'required',
            'id_card_no'      => 'required',
            'no_of_adult'     => 'required',

        ];

        if ($this->type == 'Online') {
            $arr['source'] = 'required';
        }
        if ($this->type == 'Travel Agency') {
            $arr['agent_name'] = 'required';
        }

        return $arr;
    }
}
