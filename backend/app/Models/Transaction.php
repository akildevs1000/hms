<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'time'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'date' => 'datetime:Y-m-d',
    ];

    public function getTimeAttribute()
    {
        return date('H:i', strtotime($this->attributes['date']));
    }


    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_method_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
