<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'foods';

    protected $casts = [
        'breakfast' => 'array',
        'lunch' => 'array',
        'dinner' => 'array',
    ];

    /**
     * Get the booking that owns the Food
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}