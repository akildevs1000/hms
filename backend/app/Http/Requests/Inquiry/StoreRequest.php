<?php

namespace App\Http\Requests\Inquiry;

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
            'first_name'      => 'required',
            'last_name'       => 'nullable',
            'contact_no'      => 'required|min:9|max:13',
            'email'           => 'nullable',
            'address'         => 'nullable',
            'company_id'      => 'required',
            'customer_type'   => 'nullable',
            'title'      => 'required',
            'whatsapp'   => 'required',
            'check_in'  => 'nullable',
            'check_out'  => 'nullable',
            'days' => 'nullable',
            'rooms_type'  => 'nullable',
            'number_of_rooms'  => 'nullable',
            'rooms' => 'nullable',
            'purpose' => 'nullable',
            'customer_request' => 'nullable',
            'remark' => 'nullable',
            'city' => 'nullable',
            'image' => 'nullable',

        ];

        return $arr;
    }
}
