<?php

namespace App\Models;

use App\Models\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id');
    }

    public function getTimeAttribute()
    {
        return date('H:i', strtotime($this->created_at));
    }

    /**
     * Get the booking that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}