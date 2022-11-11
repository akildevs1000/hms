<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        "check_in_status",
        "check_out_status",
        "created_at",
        "updated_at"
    ];
}
