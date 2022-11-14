<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = [
        'resourceId',
        'title',
    ];

    /**
     * Get the user that owns the Booking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function GetResourceIdAttribute()
    {
        return  Room::find($this->room_id)->room_no ?? '';
    }

    public function GetTitleAttribute()
    {
        return  Customer::find($this->customer_id)->name ?? '';
    }


    protected $casts = ['booking_date' => 'datetime:d-M-y'];

    /**
     * Get the user that owns the Booking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault([
            "name" => "---"
        ]);
    }

    // public function GetBackgroundAttribute()
    // {
    //     $roomType =  Room::with('roomType')->find($this->room_id)->roomType->name ?? '';

    //     return match ($roomType) {
    //         'Single' => 'red',
    //         'Double' => 'green',
    //         'Triple' => 'Pink',
    //         'Family' => '#000',
    //         'King' => 'blue',
    //         'Sized' => 'gray',
    //         'Single' => 'black',
    //     };
    // }
}