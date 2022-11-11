<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        "check_in_status",
        "check_out_status",
        "created_at",
        "updated_at"
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
}
