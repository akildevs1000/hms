<?php

namespace App\Http\Requests\RoomCleaning;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            "room_id" => "required",
            "status" => "required",
            "start_time" => "required",
            "end_time" => "required",
            "total_time" => "required",
            "before_attachment" => "nullable",
            "after_attachment" => "nullable",
            "voice_note" => "nullable",
            "cleaned_by_user_id" => "required",
            "response_by_user_id" => "nullable",
            "company_id" => "required",
        ];
    }
}
