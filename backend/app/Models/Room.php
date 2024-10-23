<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    const Available = 0;
    const Blocked = 1;

    use HasFactory;

    protected $guarded = [];

    protected $with = ['room_type:id,name,price,type'];

    protected $appends = [
        // 'background',
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
    public function device()
    {
        return $this->hasOne(Device::class);
    }
    /**
     * Get the bookedRoom associated with the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bookedRoom(): HasOne
    {
        return $this->hasOne(BookedRoom::class)->orderBy("id", "desc");
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

    // public function GetBackgroundAttribute()
    // {
    //     // return '#f48665';

    //     $status = $this->room_type_id ?? 0;
    //     return match ($status) {
    //         1 => '#f48665',
    //         2 => '#8e4cf1',
    //         3 => '#289cf5',
    //         4 => '#23bdb8',
    //         5 => '#23b8',
    //         20 => '#E5B3F2', //hall
    //         21 => '#E5B3F2', //hall
    //         22 => '#E5B3F2', //hall

    //         6 => '#f48665',
    //         7 => '#8e4cf1',
    //         8 => '#289cf5',
    //         9 => '#23bdb8',
    //         10 => '#F2D397',
    //         11 => '#FF5994',
    //         12 => '#E5B3F2',

    //         0 => '#E5B3F2',
    //     };
    // }

    // public function GetRoomTypeAttribute()
    // {
    //     return RoomType::find($this->room_type_id)->first()->name ?? '';
    // }

    public function GetPriceAttribute()
    {
        return RoomType::find($this->room_type_id)->price ?? '';
    }


    public function getNumberOfDays(Carbon $startDate, Carbon $endDate)
    {
        return $startDate->diffInDays($endDate);
    }

    public function is_cleaned()
    {
        return $this->hasOne(RoomCleaning::class)
            ->whereDate("created_at", date("Y-m-d"))
            ->where("status", RoomCleaning::CLEANED);
    }

    // public function isBookedForPeriod(Carbon $startDate, Carbon $endDate)
    // {
    //     return $this->bookings()
    //         ->where('start_date', '<=', $endDate->format('Y-m-d'))
    //         ->where('end_date', '>=', $startDate->format('Y-m-d'))
    //         ->exists();
    // }
}
