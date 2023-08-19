<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationReportTypes extends Model
{
    use HasFactory;
    protected $fillable = [

        'company_id',
        'name',
        'description',
    ];
}
