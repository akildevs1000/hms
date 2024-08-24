<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getIdFrontendSideAttribute($value)
    {
        if (!$value) return null;
        return asset('id_frontend_side/' . $value);
    }

    public function getIdBackendSideAttribute($value)
    {
        if (!$value) return null;
        return asset('id_backend_side/' . $value);
    }

    public function getCapturedPhotoAttribute($value)
    {
        if (!$value) return null;
        return asset('captured_photo/' . $value);
    }

    public function getSignAttribute($value)
    {
        if (!$value) return null;
        return asset('sign/' . $value);
    }
}
