<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Taxable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ["show_taxable_invoice_number"];


    /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function getShowTaxableInvoiceNumberAttribute()
    {
        return "KG-" . $this->taxable_invoice_iumber;
    }

    /**
     * Get the company that owns the Taxable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
