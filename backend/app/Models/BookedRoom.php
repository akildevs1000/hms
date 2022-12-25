<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'resourceId',
        'title',
        'background',
    ];

    protected $casts = [
        'posting_date' => 'datetime:',
    ];

    protected $with = ['postings', 'booking'];
    // protected $with = ['postings'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
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
        } else {
            (int) $status = $model->booking_status ?? 0;
        }

        return match($status) {

            1 => 'linear-gradient(135deg, #56ab2f  0, #a8e063 100%)', //paid advance
            0 => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
            2 => 'linear-gradient(135deg, #8e4cf1 0, #c554bc 100%)',
            3 => 'linear-gradient(135deg, #289cf5, #4f8bb7)',
            4 => 'linear-gradient(135deg, #34444c 0, #657177 100%)',
            5 => 'green',
            6 => 'linear-gradient(135deg, #f48665 0, #d68e41 100%)', //only booking
        };
    }

    public function SetCheckInAttribute($value)
    {
        $this->attributes['check_in'] = date('Y-m-d h:m', strtotime($value));
    }

    public function SetCheckOutAttribute($value)
    {
        $this->attributes['check_out'] = date('Y-m-d h:m', strtotime($value));
    }

    public function GetResourceIdAttribute()
    {
        return Room::find($this->room_id)->room_no ?? '';
    }
}
