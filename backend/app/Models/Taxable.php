<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Taxable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ["show_taxable_invoice_number"];

    protected $casts = [
        "created_at" => "datetime:d-M-y",
    ];

    protected $hidden = ["updated_at", "taxable_invoice_number"];



    /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function getShowTaxableInvoiceNumberAttribute()
    {
        $id = $this->company_id;
        if ($id == 1) return "TG-" . $this->taxable_invoice_number;
        else if ($id == 2) return "KG-" . $this->taxable_invoice_number;
        else if ($id == 3) return "DM-" . $this->taxable_invoice_number;
    }

    /**
     * Get the company that owns the Taxable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}
