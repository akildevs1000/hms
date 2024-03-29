<?php

namespace App\Models;

use App\Models\IdCardType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    // public function getCarNoAttribute(): string
    // {
    //     return $this->car_no != '' ? $this->car_no : '---';
    // }
    // public function getGstNumberAttribute(): string
    // {
    //     return $this->gst_number != '' ? $this->gst_number : '---';
    // }

    // public function getAddressAttribute(): string
    // {
    //     return $this->address != '' ? $this->address : "---";
    // }
    // public function getIdCardNoAttribute(): string
    // {
    //     return $this->id_card_no != '' ? $this->id_card_no : "---";
    // }
    // public function getEmailAttribute(): string
    // {
    //     return $this->email != '' ? $this->email : "---";
    // }

    public static function customerAttributes()
    {
        return [
            'first_name',
            'last_name',
            'contact_no',
            'email',
            'id_card_type_id',
            'id_card_no',
            'car_no',
            'no_of_adult',
            'no_of_child',
            'no_of_baby',
            'address',
            'company_id',
            'customer_type',
            'dob',
            'title',
            'whatsapp',
            'nationality',
            'gst_number',
        ];
    }

    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, fn($query, $search) =>
            $query->where(
                fn($query) => $query
                    ->orWhere('first_name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('contact_no', 'ILIKE', '%' . $search . '%')
                    ->orWhere('whatsapp', 'ILIKE', '%' . $search . '%')
                    ->orWhere('email', 'ILIKE', '%' . $search . '%')
                    ->orWhere('id_card_no', 'ILIKE', '%' . $search . '%')
                    ->orWhere('car_no', 'ILIKE', '%' . $search . '%')
                    ->orWhere('address', 'ILIKE', '%' . $search . '%')
            ));
    }
}
