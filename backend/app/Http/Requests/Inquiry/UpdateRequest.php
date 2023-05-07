<?php

namespace App\Http\Requests\Inquiry;

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
            'first_name'      => 'nullable',
            'last_name'       => 'nullable',
            'contact_no'      => 'nullable|min:9|max:13',
            'email'           => 'nullable',
            'address'         => 'nullable',
            'company_id'      => 'nullable',
            'customer_type'   => 'nullable',
            'title'      => 'nullable',
            'whatsapp'   => 'nullable',
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
