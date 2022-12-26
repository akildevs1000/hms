<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['paymentMode'];


    protected $appends = ['time'];

    protected $casts = [
        'created_at' => 'datetime:d-M-y',
    ];


    public function paymentMode()
    {
        return $this->belongsTo(paymentMode::class, 'payment_mode', 'id');
    }

    public function getTimeAttribute()
    {
        return date('H:i', strtotime($this->created_at));
    }
}