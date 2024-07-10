<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        "created_at" => "datetime:d-M-y"
    ];

    public function getPathAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('employee_document/' . $value);
    }
}
