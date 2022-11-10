<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $appends = ["title"];

    public function getTitleAttribute()
    {
        return Str::replace('_', ' ', $this->name);
    }
}