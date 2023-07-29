<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostAndFoundItems extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'booking_id', 'item_name', 'missing_datetime', 'missing_notes', 'missing_remarks'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getFoundImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/customer/lost_and_found/' . $value);
    }
    public function getRecoveredImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/customer/lost_and_found/' . $value);
    }

}
