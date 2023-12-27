<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLogs extends Model
{
    use HasFactory;
    protected $table = 'device_logs';

    protected $casts = [
        'created_at' => 'datetime:d-M-y H:m',

    ];
    protected $fillable = [

        'serial_number',
        'status',
        'raw_data',
        'log_time',


    ];
}
