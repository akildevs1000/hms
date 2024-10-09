<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ["data" =>"array"];
}
