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
        // 'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function GetTitleAttribute()
    {
        return  Customer::find($this->customer_id)->full_name ?? '';
    }

    public function GetBackgroundAttribute()
    {
        // $status =   Room::find($this->room_id)->status ?? '1';
        (int)$status =   Booking::find($this->booking_id)->booking_status ?? 0;
        // dd($status);
        return    match ($status) {
            0 => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
            1 => 'linear-gradient(135deg, #f48665 0, #d68e41 100%)',
            2 => 'linear-gradient(135deg, #8e4cf1 0, #c554bc 100%)',
            3 => 'linear-gradient(135deg, #289cf5, #4f8bb7)',
            4 => 'linear-gradient(135deg, #34444c 0, #657177 100%)',
            5 => 'green',
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
        return  Room::find($this->room_id)->room_no ?? '';
    }
}