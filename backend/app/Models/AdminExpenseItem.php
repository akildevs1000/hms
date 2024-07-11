<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminExpenseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tax' => 'integer',
    ];
    
}
