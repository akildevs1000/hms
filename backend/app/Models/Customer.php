<?php

namespace App\Models;

use App\Models\IdCardType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'full_name',
        'document_name',
    ];



    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    /**
     * Get the booking that owns the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->hasOne(Booking::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function idCardType(): BelongsTo
    {
        return $this->belongsTo(IdCardType::class);
    }

    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/customer/photo/' . $value);
    }

    public function getDocumentAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/customer/' . $value);
    }

    public function getDocumentNameAttribute()
    {
        if ($this->attributes['document'] ?? false) {
            return $this->attributes['document'];
        }
        return null;
    }
}