<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportNotification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'body' => 'array',
        'reports' => 'array',
        'mediums' => 'array',
        'tos' => 'array',
        'ccs' => 'array',
        'bccs' => 'array',
    ];
}
