<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class ValidationRequest extends FormRequest
{
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
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'vendor_display_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'work_phone' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'vendor_category_id' => 'required|exists:vendor_categories,id'
        ];
    }
}
