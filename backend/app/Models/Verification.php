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
        $value = $this->id_frontend_side;
        if (!$value) return null;
        return asset('id_frontend_side/' . $value);
    }

    public function getIdBackendSideUrlAttribute($value)
    {
        $value = $this->id_backend_side;
        if (!$value) return null;
        return asset('id_backend_side/' . $value);
    }

    public function getCapturedPhotoUrlAttribute($value)
    {
        $value = $this->captured_photo;
        if (!$value) return null;
        return asset('captured_photo/' . $value);
    }

    public function getSignUrlAttribute($value)
    {
        $value = $this->sign;
        if (!$value) return null;
        return asset('sign/' . $value);
    }
}
