<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessages extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        //public hotel
        return asset('hotel/chat/' . $this->company_id . '/' . $this->booking_room_id . '/' . $this->filename);
    }
}
