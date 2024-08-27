<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "created_at" => "datetime:d-M-y",
        'items' => 'array',
    ];

    public function getTotalAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     * Get the customer that owns the Quotation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all of the followups for the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followups()
    {
        return $this->hasMany(Followup::class)->latest();
    }

    /**
     * Get all of the followups for the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function status()
    {
        return $this->hasOne(Followup::class)->latest();
    }
}
