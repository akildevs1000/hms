<?php

namespace App\Http\Requests\Booking;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class  DocumentRequest extends FormRequest
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


        $arr = [];


        if ($this->document) {
            $arr = [
                'document' => 'mimes:jpeg,png,pdf',
                'image' => 'mimes:jpeg,png,pdf|max:1024',
                'id_card_type_id' => 'required',
                'id_card_no'      => 'required',
                'passport_expiration'      => 'required',
            ];
        }


        return $arr;
    }
}