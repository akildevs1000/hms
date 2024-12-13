<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inquiry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'check_in' => 'datetime:Y-m-d',
        'check_out' => 'datetime:Y-m-d',
    ];

    protected $appends = [
        'full_name',
        'document_name',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the Quotation associated with the Inquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quotation()
    {
        return $this->hasOne(Quotation::class)->latest();
    }

    /**
     * Get the booking that owns the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
    public function business_source()
    {
        return $this->belongsTo(BusinessSource::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

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
                ->orWhere('first_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('last_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('contact_no', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('whatsapp', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('email', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
                ->orWhere('city', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%')
        ));
    }


    public static function headers()
    {
        return [
            [
                'text' => '#',
                'value' => 'id',
            ],
            [
                'text' => 'Business Source',
                'value' => 'business_source',
            ],
            [
                'text' => 'Room Type',
                'value' => 'room_type',
            ],
            [
                'text' => 'Source Type',
                'value' => 'source_type',
            ],
            [
                'text' => 'Source',
                'value' => 'source',
            ],
            [
                'value' => 'first_name',
                'text' => 'First Name',
            ],
            [
                'value' => 'check_in',
                'text' => 'C/In',
            ],
            [
                'value' => 'check_out',
                'text' => 'C/Out',
            ],
            [
                'value' => 'days',
                'text' => 'Days',
            ],
            [
                'value' => 'number_of_rooms',
                'text' => 'N/Rooms',
            ],
            [
                'value' => 'quotation',
                'text' => 'Quotation',
            ],
            [
                'value' => 'inquiry_type',
                'text' => 'Type',
            ],
            [
                'value' => 'options',
                'text' => 'Action',
            ],
        ];
    }
}
