<?php

namespace App\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CancelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
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
        "reason",
        "cancel_by",
        "booking_id",
        "action",
        "customer_id",
        "status_before_cancelation",
        "status_before_cancelation_msg",
    ];

    protected $appends = [
        'time',
    ];

    public function getTImeAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->created_at));
    }


    /**
     * Get the user that owns the CancelRoom
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'cancel_by');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
