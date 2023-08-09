<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = [
        'resourceId',
        'title',
        'background',
        'status',
        'check_in_date',
        'check_out_date',
    ];
    protected $casts = [
        'booking_date' => 'datetime:Y-m-d',
        'check_in_date' => 'datetime:d-M-y H:i',
        'check_out_date' => 'datetime:d-M-y H:i',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookedRooms()
    {
        return $this->hasMany(BookedRoom::class);
    }

    public function orderRooms()
    {
        return $this->hasMany(OrderRoom::class);
    }

    public function GetResourceIdAttribute()
    {
        return Room::find($this->room_id)->room_no ?? '';
    }

    public function getCheckInDateAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->check_in));
    }

    public function getCheckOutDateAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->check_out));
    }

    public function GetTitleAttribute()
    {
        return Customer::find($this->customer_id)->full_name ?? '';
    }

    public function GetBackgroundAttribute()
    {
        $status = Room::find($this->room_id)->status ?? '0';
        return match ($status) {
            '0' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
            '1' => 'linear-gradient(135deg, #f48665 0, #d68e41 100%)',
            '2' => 'linear-gradient(135deg, #8e4cf1 0, #c554bc 100%)',
            '3' => 'linear-gradient(135deg, #289cf5, #4f8bb7)',
            '4' => 'linear-gradient(135deg, #34444c 0, #657177 100%)',
            '5' => 'green',
        };
    }

    public function GetStatusAttribute()
    {
        return Room::find($this->room_id)->status ?? '';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault([
            "name" => "---",
        ]);
    }

    public function SetCheckInAttribute($value)
    {
        // $this->attributes['check_in'] = $value . ' ' . date('H:i:s');
        if (session('isCheckInSes')) {
            $cod = $this->attributes['check_in'] = date('Y-m-d H:i', strtotime($value));
            BookedRoom::whereBookingId($this->attributes['id'])->update(['check_in' => $cod, 'booking_status' => 2]);
        } else {
            $this->attributes['check_in'] = $value . ' ' . date('H:i:s');
        }
    }

    public function SetReferenceNoAttribute($value)
    {
        $this->attributes['reference_no'] = Str::lower($value);
    }

    public function SetCheckOutAttribute($value)
    {
        if (session('isCheckoutSes')) {
            $cod = $this->attributes['check_out'] = date('Y-m-d H:i', strtotime($value));
            BookedRoom::whereBookingId($this->attributes['id'])->update(['check_out' => $cod, 'booking_status' => 3]);
        } else {
            $cod = $this->attributes['check_out'] = date('Y-m-d 11:00', strtotime($value));
        }

        // dd($cod);

        // $date = Carbon::parse($value);
        // $date->addDays(1);
        // $d = $date->format('Y-m-d');
        // $this->attributes['check_out'] = $d . ' ' . date('11:00:00');
    }

    // public function GetBackgroundAttribute()
    // {
    //     $roomType =  Room::with('roomType')->find($this->room_id)->roomType->name ?? '';

    //     return match ($roomType) {
    //         'Single' => 'red',
    //         'Double' => 'green',
    //         'Triple' => 'Pink',
    //         'Family' => '#000',
    //         'King' => 'blue',
    //         'Sized' => 'gray',
    //         'Single' => 'black',
    //     };
    // }

    public function getDocumentAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/booking/' . $value);
    }

    public function scopeFilter($query, $filter)
    {
        $query->when($filter ?? false, fn($query, $search) =>
            $query->where(
                fn($query) => $query
                    ->orWhere('reservation_no', 'ILIKE', '%' . $search . '%')
                    ->orWhere('reference_no', 'ILIKE', '%' . $search . '%')
                    ->orWhere('type', 'ILIKE', '%' . $search . '%')
                    ->orWhereHas(
                        'customer',
                        fn($query) =>
                        $query->Where('first_name', 'ILIKE', '%' . $search . '%')
                            ->orWhere('last_name', 'ILIKE', '%' . $search . '%')
                            ->orWhere('title', 'ILIKE', '%' . $search . '%')
                            ->orWhere('whatsapp', 'ILIKE', '%' . $search . '%')
                            ->orWhere('contact_no', 'ILIKE', '%' . $search . '%')
                    )
            ));
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function cityLedgerPayments()
    {
        return $this->hasMany(Payment::class)->where('is_city_ledger', 1);
    }

    public function withOutCityLedgerPayments()
    {
        return $this->hasMany(Payment::class)->where('is_city_ledger', 0);
    }

    public static function bookingAttributes()
    {
        return [
            "customer_id",
            "booking_status",
            "customer_type",
            "customer_status",
            "all_room_Total_amount",
            "total_extra",
            "type",
            "source",
            "agent_name",
            "check_in",
            "check_out",
            "discount",
            "advance_price",
            "payment_mode_id",
            "total_days",
            "sub_total",
            "after_discount",
            "sales_tax",
            "total_price",
            "remaining_price",
            "request",
            "company_id",
            "remark",
            "rooms",
            "reference_no",
            "paid_by",
            "purpose",
            "gst_number",
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope('order', function (Builder $builder) {
    //         $builder->orderBy('id', 'desc');
    //     });
    // }
}
