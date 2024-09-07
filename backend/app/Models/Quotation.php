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

    public function getSubTotalAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getDiscountAttribute($value)
    {
        return number_format($value, 2);
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

    /**
     * Get the customer that owns the Quotation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class)->with("user:id,company_id,email");
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

    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ref_no',env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%') // Adjust field as needed
                              ->orWhere('email', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%'); // Adjust field as needed
                    });
            });
        });
    }
}


