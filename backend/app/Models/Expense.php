<?php

namespace App\Models;

use App\Models\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['paymentMode'];
    protected $appends = ['time'];

    protected $casts = ['created_at' => 'datetime:d-M-y'];


    /**
     * Get the paymentMode that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_modes', 'id');
    }

    public function getTimeAttribute()
    {
        return date('H:i', strtotime($this->created_at));
    }

    public function getDocumentAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/expense/' . $value);
    }

    public function getDocument1Attribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/expense/' . $value);
    }

    public function getDocument2Attribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/expense/' . $value);
    }

    public function getDocument3Attribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/expense/' . $value);
    }
}
