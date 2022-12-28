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
            'last_name'       => 'nullable',
            'contact_no'      => 'required|min:10|max:13',
            'email'           => 'required',
            'id_card_type_id' => 'required',
            'id_card_no'      => 'required',
            'car_no'          => 'nullable',
            'no_of_adult'     => 'nullable',
            'no_of_child'     => 'nullable',
            'no_of_baby'      => 'nullable',
            'address'         => 'nullable',
            'company_id'      => 'required',

            'title'      => 'required',
            'whatsapp'      => 'nullable',
            'nationality'      => 'required',
        ];

        return $arr;
    }
}