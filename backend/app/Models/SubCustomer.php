<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCustomer extends Model
{
    use HasFactory;

    protected $guarded = [];

    // /**
    //  * Get the Customer that owns the SubCustomer
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  */
    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }
}
