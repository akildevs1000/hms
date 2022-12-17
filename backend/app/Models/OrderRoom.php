<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The roles that belong to the OrderRoom
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postings()
    {
        // dd($this->date);
        return $this->hasMany(Posting::class, 'booked_room_id', 'booked_room_id');
    }
}