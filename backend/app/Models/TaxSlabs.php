<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSlabs extends Model
{
    use HasFactory;

    protected $fillable = [

        'company_id',
        'start_price',
        'end_price',
        'tax',
    ];
}
