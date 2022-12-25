<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['room_type:id,name,price'];

    protected $appends = [
        'background',
        // "room_type",
        "price",
    ];

    protected $hidden = [
        "check_in_status",
        "check_out_status",
        "updated_at",
        "updated_at",
    ];

    /**
     * Get all of the comments for the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the bookedRoom associated with the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bookedRoom(): HasOne
    {
        return $this->hasOne(BookedRoom::class);
    }

    /**
     * Get the room_type that owns the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room_type()
    {
        return $this->belongsTo(RoomType::class)->withDefault([
            "name"  => "---",
            "price" => "---",
        ]);
    }

    public function GetBackgroundAttribute()
    {
        // return '#f48665';

        $status = $this->room_type_id ?? '';
        return match($status) {
            1 => '#f48665',
            2 => '#8e4cf1',
            3 => '#289cf5',
            4 => '#23bdb8',
        };
    }

    // public function GetRoomTypeAttribute()
    // {
    //     return RoomType::find($this->room_type_id)->first()->name ?? '';
    // }

    public function GetPriceAttribute()
    {
        return RoomType::find($this->room_type_id)->price ?? '';
    }
}
