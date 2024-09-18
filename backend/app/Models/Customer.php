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

    public function latest_booking()
    {
        return $this->hasOne(Booking::class)->latest()
            ->with(["orderRooms" => function ($q) {
                $q->with("foodplan");
            }]);
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

            'id_frontend_side',
            'id_backend_side',


            'country',
            'state',
            'city',
            'zip_code',

        ];
    }

    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, fn($query, $search) =>
        $query->where(
            fn($query) => $query
                ->orWhere('first_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('last_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('contact_no', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('whatsapp', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('email', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('id_card_no', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('car_no', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('address', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
        ));
    }

    public function getIdFrontendSideAttribute($value)
    {
        if (!$value) return null;
        // return "https://amcbackend.mytime2cloud.com/sign/" . $value;
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
