<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelOrdersFood extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $fillable = [

        'company_id',
        'booking_id',
        'room_id',
        'food_item_id',
        'food_price',
        'food_sgst',
        'food_cgst',
        'food_total',
        'delivery_datetime',
        'status',
        'reason_cancelled',
        'cancelled_datetime',
        'qty',
        'request_datetime',
        'booking_rooms_id'

    ];

    public function food()
    {
        return $this->hasOne(HotelFoodItems::class,   "id", "food_item_id");
    }
    public function booking()
    {
        return $this->hasOne(Booking::class,   "id", "booking_id");
    }
    public function room()
    {
        return $this->hasOne(Room::class,   "id", "room_id");
    }
}
