<?php

namespace App\Http\Requests\LostAndFoundItems;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'company_id' => 'required',
            'booking_id' => 'required',
            'item_name' => 'required',
            'missing_datetime' => 'required',
            'missing_remarks' => 'nullable',
            'missing_notes' => 'nullable',
            'user_id' => 'nullable',

        ];
    }
}
