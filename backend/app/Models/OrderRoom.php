<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRoom extends Model
{
    use HasFactory;

    // protected $guarded = [];

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
        "booking_id",
        "customer_id",
        "date",
        "booked_room_id",
        "price_adjusted_after_dsicount",

        "food_plan_id",
        "extra_bed_qty",
        "early_check_in",
        "late_check_out",
        "food_plan_price",


        "cleaning",
        "electricity",
        "generator",
        "audio",
        "projector",

        "hall_min_hours",
        "extra_hours",
        "total_booking_hours",
        "extra_booking_hours_charges",

        "breakfast",
        "lunch",
        "dinner",


        "tariff",
        "day",
        "base_price",

        "miscellaneous_total",
        "miscellaneous_total_without_tax",
        "miscellaneous_tax",
        "single_day_extra_amount",
        "single_day_discount",
        "inv_room_listing_price",
        "inv_room_cgst",
        "inv_room_sgst",
        "inv_room_tax_per",
        "inv_food_tax_per",
        "room_change_notes"



    ];

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
            "room_extra_amount",
            "extra_amount_reason",

            "base_price",
            "tariff"
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('room_no', 'desc');
        });
    }

    public function foodplan()
    {
        return $this->belongsTo(FoodPlan::class, "food_plan_id");
    }

    public function getDayAttribute()
    {
        return date('l', strtotime($this->attributes['date']));
    }


    public function getDateAttribute()
    {
        return date('d-M-y', strtotime($this->attributes['date']));
    }

    public function getCheckInAttribute()
    {
        return date('Y-m-d 12:00', strtotime($this->attributes['check_in']));
    }

    public function getCheckOutAttribute()
    {
        return date('Y-m-d 11:00', strtotime($this->attributes['check_out']));
    }
}
