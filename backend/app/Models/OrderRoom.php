<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The roles that belong to the OrderRoom
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postings()
    {
        // dd($this->date);
        return $this->hasMany(Posting::class, 'booked_room_id', 'booked_room_id');
    }

    public static function orderRoomAttributes()
    {
        return [
            "room_no",
            "room_type",
            "room_id",
            "price",
            "days",
            "sgst",
            "cgst",
            "check_in",
            "check_out",
            "bed_amount",
            "room_discount",
            "after_discount",
            "room_tax",
            "total_with_tax",
            "total",
            "grand_total",
            "company_id",
            "no_of_adult",
            "no_of_child",
            "no_of_baby",
            "tot_adult_food",
            "tot_child_food",
            "discount_reason",
            "meal",
            "booking_id",
            "customer_id",
        ];
    }
}
