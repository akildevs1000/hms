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
        'background',
        'status',
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

    public function bookedRooms()
    {
        return $this->hasMany(BookedRoom::class);
    }

    public function GetResourceIdAttribute()
    {
        return  Room::find($this->room_id)->room_no ?? '';
    }

    public function GetTitleAttribute()
    {
        return  Customer::find($this->customer_id)->full_name ?? '';
    }

    public function GetBackgroundAttribute()
    {
        $status =   Room::find($this->room_id)->status ?? '0';
        return    match ($status) {
            '0' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
            '1' => 'linear-gradient(135deg, #f48665 0, #d68e41 100%)',
            '2' => 'linear-gradient(135deg, #8e4cf1 0, #c554bc 100%)',
            '3' => 'linear-gradient(135deg, #289cf5, #4f8bb7)',
            '4' => 'linear-gradient(135deg, #34444c 0, #657177 100%)',
            '5' => 'green',
        };
    }

    public function GetStatusAttribute()
    {
        return Room::find($this->room_id)->status ?? '';
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

    public function SetCheckInAttribute($value)
    {
        $this->attributes['check_in'] = date('Y-m-d h:m', strtotime($value));
    }

    public function SetCheckOutAttribute($value)
    {
        $this->attributes['check_out'] = date('Y-m-d h:m', strtotime($value));
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

    public function getDocumentAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('documents/booking/' . $value);
    }

    public function scopeFilter($query,  $filter)
    {
        $query->when($filter ?? false, fn ($query, $search) =>
        $query->where(
            fn ($query) => $query
                ->orWhere('id', 'Like', '%' . $search . '%')
                ->orWhere('type', 'Like', '%' . $search . '%')
                ->orWhereHas(
                    'customer',
                    fn ($query) =>
                    $query->Where('first_name', 'Like', '%' . $search . '%')
                        ->orWhere('last_name', 'Like', '%' . $search . '%')
                )
        ));
    }
}