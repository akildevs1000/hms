<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the payment_mode that owns the ExpensePayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_mode()
    {
        return $this->belongsTo(PaymentMode::class, "name", "payment_mode");
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->with("vendor_category");
    }
}
