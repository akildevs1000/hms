<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'id_frontend_side_url',
        'id_backend_side_url',
        'captured_photo_url',
        'sign_url'
    ];

    public function getIdFrontendSideUrlAttribute($value)
    {
        if (!$value) return null;
        return asset('id_frontend_side/' . $value);
    }

    public function getIdBackendSideUrlAttribute($value)
    {
        if (!$value) return null;
        return asset('id_backend_side/' . $value);
    }

    public function getCapturedPhotoUrlAttribute($value)
    {
        if (!$value) return null;
        return asset('captured_photo/' . $value);
    }

    public function getSignUrlAttribute($value)
    {
        if (!$value) return null;
        return asset('sign/' . $value);
    }
}
