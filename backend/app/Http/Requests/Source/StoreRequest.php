<?php

namespace App\Http\Requests\Source;

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
            'name'      => 'required',
            'type'      => 'required',
            'contact_name'      => 'nullable',
            'mobile'      => 'nullable',
            'email'      => 'nullable',
            'landline'      => 'nullable',
            'address'      => 'nullable',
            'gst'      => 'nullable',
            'company_id'      => 'required',
        ];

        return $arr;
    }
}
