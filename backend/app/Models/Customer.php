<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'full_name'
    ];



    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
