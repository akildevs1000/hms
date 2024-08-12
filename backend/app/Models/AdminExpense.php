<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminExpense extends Model
{
    const ManagementExpense = 1;
    const NonManagementExpense = 0;

    const CASH = "Cash";
    const CARD = "Card";
    const ONLINE = "Online";
    const BANK = "Bank";
    const UPI = "UPI";
    const CHEQUE = "Cheque";
    const CITYLEDGER = "City Ledger";


    use HasFactory;

    protected $guarded = [];


    protected $appends = ["date","time","datetime"];

    protected $casts = [
        "created_at" => "datetime:d-M-y"
    ];

    /**
     * Get the vendor that owns the AdminExpense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get all of the comments for the AdminExpense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(AdminExpenseItem::class);
    }

    /**
     * Get all of the comments for the AdminExpense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(AdminExpenseAttachment::class);
    }

    /**
     * Get the user associated with the AdminExpense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(ExpensePayment::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentMode::class,"name","payment_mode");
    }

    public function getDateAttribute()
    {
        return date("d-M-y", strtotime($this->created_at));
    }

    public function getTimeAttribute()
    {
        return date("H:i", strtotime($this->created_at));
    }

    public function getDatetimeAttribute()
    {
        return date("d-M-y H:i", strtotime($this->created_at));
    }
}
