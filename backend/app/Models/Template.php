<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $casts = [
        "created_at" => "datetime:d-M-y",
        "updated_at" => "datetime:d-M-y",
    ];

    protected $guarded = [];
    
    const INQUERY_CREATE = 1;
    const QUOTATION_CREATE = 2;
    const ONE_DAY_BEFORE_ARRIVAL = 3;
    const ON_ARRIVAL_DATE = 4;
    const WHEN_CUSTOMER_ARRIVED = 5; // pending
    const ON_CHECKOUT_DATE_CHECKOUT_REMINDER = 6; // pending
    const AFTER_CHECKOUT = 7; // pending
    const BIRTHDAY_WISH = 8;
    const FESTIVAL_MESSAGE = 9;
    const UNKNOWN = 0;


    const TEMPLATE_TYPES = [
        1 => "inquery_create",
        2 => "quotation_create",
        3 => "1_day_before_arrival",
        4 => "on_arrival_date",
        5 => "when_customer_arrived",
        6 => "on_checkout_date_checkout_reminder",
        7 => "after_checkout",
        8 => "birthday_wish",
        9 => "festival_message",
        0 => "unknown",
    ];

    protected $appends = ['action'];

    public function getActionAttribute()
    {
        return self::TEMPLATE_TYPES[$this->action_id];
    }

    const validateFields = [
        "name" => "required|max:50",
        "salutation" => "required|max:100",
        "body" => "required|max:1000",
        "attachment" => "nullable",
        "action_id" => "required",
        "company_id" => "required",
        "medium" => "required"
    ];
}
