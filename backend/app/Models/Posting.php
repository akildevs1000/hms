<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d-M-y',
        'posting_date' => 'datetime:d-M-y H:m',
    ];

    /**
     * Get the booking that owns the Posting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get all of the bookedRoom for the Posting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookedRoom()
    {
        return $this->belongsTo(BookedRoom::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
