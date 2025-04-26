<?php

namespace App\Models;

use App\Models\PaymentMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with    = ['paymentMode'];

    protected $appends = ['time'];

    protected $casts = [
        // 'created_at' => 'datetime:d-M-y',
    ];

    public function cash()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::CASH);
    }

    public function card()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::CARD);
    }

    public function online()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::ONLINE);
    }

    public function bank()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::BANK);
    }

    public function upi()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::UPI);
    }

    public function cheque()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::CHEQUE);
    }

    public function city_ledger()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id')->where("id", PaymentMode::CITYLEDGER);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id');
    }

    public function payment_type()
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
        return $this->belongsTo(Booking::class)
            ->withSum('transactions', 'debit');
    }
}
