<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [

        'company_id',
        'name',
        'address',
        'contact_numbers',
        'notes',

    ];
}
