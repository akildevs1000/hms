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

    const INQUERY_CREATE                     = 1;  // done
    const QUOTATION_CREATE                   = 2;  //  done
    const ONE_DAY_BEFORE_ARRIVAL             = 3;  // done
    const ON_ARRIVAL_DATE                    = 4;  // done
    const WHEN_CUSTOMER_ARRIVED              = 5;  // done
    const ON_CHECKOUT_DATE_CHECKOUT_REMINDER = 6;  // done
    const AFTER_CHECKOUT                     = 7;  // done
    const BIRTHDAY_WISH                      = 8;  // done
    const FESTIVAL_MESSAGE                   = 9;  // hold
    const BOOKING_CREATE                     = 10; // done
    const UNKNOWN                            = 0;

    const TEMPLATE_TYPES = [
        1  => "inquery_create",
        2  => "quotation_create",
        3  => "1_day_before_arrival",
        4  => "on_arrival_date",
        5  => "when_customer_arrived",
        6  => "on_checkout_date_checkout_reminder",
        7  => "after_checkout",
        8  => "birthday_wish",
        9  => "festival_message",
        10 => "booking_create",
        0  => "unknown",
    ];

    const TAGS = [
        1  => ['[title]', '[full_name]', '[from_date]', '[to_date]', '[room_type]'],
        2  => ['[title]', '[full_name]', '[from_date]', '[to_date]', '[room_type]'],
        3  => ['[title]', '[full_name]', '[reservation]'],
        4  => ['[title]', '[full_name]', '[reservation]'],
        5  => ['[title]', '[full_name]', '[reservation]'],
        6  => ['[title]', '[full_name]', '[reservation]'],
        7  => ['[title]', '[full_name]', '[reservation]'],
        8  => ['[title]', '[full_name]'],
        9  => ['[title]', '[full_name]', '[festival]'],
        10 => ['[title]', '[full_name]', '[from_date]', '[to_date]', '[room_type]', '[room_no]', '[room_no]', '[reservation]'],
    ];

    const DEFAULT_MESSAGES = [
        1  => "Thank you for your inquiry! Our team will get back to you shortly.",                                     // inquiry_create
        2  => "Thank you for requesting a quotation. We are preparing it and will share it with you soon.",             // quotation_create
        3  => "Reminder: Your arrival is just one day away! We are excited to welcome you.",                            // 1_day_before_arrival
        4  => "Today is your arrival day! We are thrilled to have you. Please let us know if you need any assistance.", // on_arrival_date
        5  => "Welcome! We are delighted you have arrived safely. Enjoy your stay with us.",                            // when_customer_arrived
        6  => "Reminder: Your checkout is today. We hope you enjoyed your stay! Kindly complete the checkout process.", // on_checkout_date_checkout_reminder
        7  => "Thank you for staying with us! We hope you had a wonderful experience. Safe travels and see you again!", // after_checkout

        8  => "🎉 Happy Birthday! 🎂\n\n"
        . "Wishing you a day filled with happiness, laughter, and all the things you love the most!\n"
        . "May this year bring you success, good health, and countless joyful moments.\n\n"
        . "Enjoy your special day! 🥳\n"
        . "Regards, Mytime2Cloud", // birthday_wish

        9  => "Wishing you and your loved ones a joyful festival filled with happiness and prosperity!", // festival_message
        10 => "Your booking has been successfully created! We look forward to serving you.",             // booking_create
    ];

    protected $appends = ['action'];

    public function getActionAttribute()
    {
        return self::TEMPLATE_TYPES[$this->action_id];
    }

    const validateFields = [
        "name"       => "required|max:50",
        "salutation" => "nullable|max:100",
        "body"       => "required|max:1000",
        "attachment" => "nullable",
        "action_id"  => "required",
        "company_id" => "required",
        "medium"     => "nullable",
    ];
}
