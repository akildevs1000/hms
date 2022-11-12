<?php

namespace App\Http\Requests\Customer;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

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

        $arr = [
            'first_name'      => 'required',
            'last_name'       => 'required',
            'contact_no'      => 'required',
            'email'           => 'required',
            'id_card_type_id' => 'required',
            'id_card_no'      => 'required',
            'car_no'          => 'required',
            'no_of_adult'     => 'required',
            'no_of_child'     => 'required',
            'no_of_baby'      => 'required',
            'address'         => 'required',
            'company_id'      => 'required',
        ];

        return $arr;
    }
}
