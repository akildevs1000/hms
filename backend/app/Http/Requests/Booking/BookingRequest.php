<?php

namespace App\Http\Requests\Booking;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'type'            => 'required',
            // 'room_type'       => 'required',
            // 'room_id'         => 'required',
            'check_in'        => 'required',
            'check_out'       => 'required',
            'discount'        => 'required',
            'advance_price'   => 'required',
            'payment_mode_id' => 'required',
            'total_days'      => 'required',
            'sub_total'       => 'required',

            'after_discount' => 'required',
            'sales_tax' => 'required',
            'total_price' => 'required',
            'remaining_price' => 'required',

            'request'     => 'nullable',
            'company_id'     => 'required',
            // 'customer_id'     => 'required',

        ];

        if ($this->type == 'Online' || $this->type == 'Travel Agency') {
            $arr['source'] = 'required';
            $arr['reference_no'] = 'required';
            $arr['paid_by'] = 'required';
        }
        // if ($this->type == 'Travel Agency') {
        //     $arr['source'] = 'required';
        //     $arr['reference_no'] = 'required';
        //     $arr['paid_by'] = 'required';
        // }

        return $arr;
    }
}