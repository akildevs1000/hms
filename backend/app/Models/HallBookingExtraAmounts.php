<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallBookingExtraAmounts extends Model
{
    use HasFactory;
    protected $table = 'hall_booking_extra_amounts';



    protected $fillable = [

        'booking_id',
        'company_id',
        'name',
        'qty',
        'total',
        'price_per_item',

    ];
}
