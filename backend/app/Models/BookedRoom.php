<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedRoom extends Model
{
    use HasFactory;

    protected $guarded = [];


    // protected $fillable = [
    //     "room_no",
    //     "room_type",
    //     "room_id",
    //     "price",
    //     "days",
    //     "sgst",
    //     "cgst",
    //     "check_in",
    //     "check_out",
    //     "bed_amount",
    //     "room_discount",
    //     "after_discount",
    //     "room_tax",
    //     "total_with_tax",
    //     "total",
    //     "grand_total",
    //     "company_id",
    //     "no_of_adult",
    //     "no_of_child",
    //     "no_of_baby",
    //     "tot_adult_food",
    //     "tot_child_food",
    //     "discount_reason",
    //     "meal"
    // ];

    protected $appends = [
        'resourceId',
        'title',
        'background',
        'check_out_time',
        'end',
    ];

    protected $casts = [
        'posting_date' => 'datetime:',
    ];

    protected $with = ['postings', 'booking'];

    public function getAppends()
    {
        return array_merge($this->with, $this->appends);
    }

    public function getCustomAppends()
    {
        $custom = [
            'created_at',
            'updated_at',
            'booking_status',
        ];
        return array_merge($this->with, $this->appends, $custom);
    }

    public function getWithoutAppends()
    {
        $attributes = $this->attributesToArray();

        unset($attributes['check_out_time']);
        unset($attributes['end']);
        unset($attributes['background']);
        unset($attributes['resourceId']);
        unset($attributes['title']);
        unset($attributes['booking_status']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);

        return $attributes;
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function GetTitleAttribute()
    {
        return Customer::find($this->customer_id)->full_name ?? '';
    }

    /**
     * Get all of the posts for the BookedRoom
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postings()
    {
        return $this->hasMany(Posting::class);
    }
    /**
     * Get all of the comments for the BookedRoom
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function roomType()
    {
        return $this->hasOneThrough(RoomType::class, BookedRoom::class, 'room_id', 'id', 'room_id', 'room_id');
    }
    public function GetBackgroundAttribute()
    {
        $model = Booking::find($this->booking_id);

        if ($model->booking_status == 1 && $model->advance_price == 0) {
            (int) $status = 6;
        } else if (($model->booking_status == 2) && (date('Y-m-d', strtotime($model->check_out)) <= date('Y-m-d'))) {
            (int) $status = 7;
        } else {
            (int) $status = $model->booking_status ?? 0;
        }

        return match ($status) {

            1 => 'linear-gradient(135deg, #02ADA4  0, #02ADA4 100%)', //paid advance
            0 => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)', //available room
            // 2 => 'linear-gradient(135deg, #F95C39 0, #F95C39 100%)', //check in room
            2 => 'linear-gradient(135deg, #800000 0, #800000 100%)', //check in room
            // 3 => 'linear-gradient(135deg, #4390FC, #4390FC)',
            // 3 => 'linear-gradient(135deg, #d66d75   0, #e29587 100%)', //dirty room
            3 => 'linear-gradient(135deg, #ff0000   0, #ff0000 100%)', //dirty room
            4 => 'linear-gradient(135deg, #34444c 0, #657177 100%)',
            5 => 'green',
            6 => 'linear-gradient(135deg, #FFBE00 0, #FFBE00 100%)', //only booking
            7 => 'linear-gradient(135deg, #4390FC      0, #4390FC 100%)', //expect check out
        };
    }

    public function SetCheckInAttribute($value)
    {
        // $this->attributes['check_in'] = date('Y-m-d h:m', strtotime($value));

        $this->attributes['check_in'] = $value . ' ' . date('H:i:s');
    }

    public function SetCheckOutAttribute($value)
    {
        // dd($this->attributes['check_out'] = date('Y-m-d 11:00', strtotime($value)));
        $this->attributes['check_out'] = date('Y-m-d 11:00', strtotime($value));

        // $date = Carbon::parse($value);
        // $date->addDays(1);
        // $d = $date->format('Y-m-d');
        // $this->attributes['check_out'] = $d . ' ' . date('11:00:00');

    }

    public function GetCheckOutTimeAttribute()
    {
        // CheckOUtDateForEachDate
        $time = $this->check_out;

        return date('H:i', strtotime($time));
    }

    public function GetEndAttribute()
    {
        $date = date_create($this->check_out);
        // date_modify($date, "-1 days");
        return date_format($date, "Y-m-d");
        // return date_format($date, "Y-m-d H:i");
    }

    public function GetResourceIdAttribute()
    {
        return Room::find($this->room_id)->room_no ?? '';
    }

    public static function bookedRoomAttributes()
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
        ];
    }
}
