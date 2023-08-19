<?php

namespace App\Http\Requests\EmailNotifications;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmailNotificationsRequest extends FormRequest
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
        return [
            'company_id' => 'required',
            'email' => 'required|email|min:10|max:191|unique:email_notifications',
            'status' => 'required',
            'name' => 'required',
            'whatsapp_number' => ['nullable', 'min:10', 'max:13'],

        ];
    }
}
