<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime:d-M-y',
    ];

    protected $appends = [
        'day'
    ];


    protected $fillable = [
        'company_id',
        'date',
        'sold',
        'unsold',
        'order_room_id',
    ];


    public function getDayAttribute()
    {
        return  date('d', strtotime($this->date));
    }
}