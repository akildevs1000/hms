<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return  Room::find($this->room_id)->room_no ?? '';
    }

    public function getCheckInDateAttribute()
    {
        return  date('Y-m-d H:i', strtotime($this->check_in));
    }

    public function getCheckOutDateAttribute()
    {
        return  date('Y-m-d H:i', strtotime($this->check_out));
    }

    public function GetTitleAttribute()
    {
        return  Customer::find($this->customer_id)->full_name ?? '';
    }

    public function GetBackgroundAttribute()
    {
        $status =   Room::find($this->room_id)->status ?? '0';
        return    match ($status) {
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
            "name" => "---"
        ]);
    }

    public function SetCheckInAttribute($value)
    {
        // dd($value . ' ' . date('H:i:s'));
        // dd(date('Y-m-d H:i:s', strtotime($value)));
        // dd($value);
        // $this->attributes['check_in'] = date('Y-m-d h:m', strtotime($value));
        $this->attributes['check_in'] = $value . ' ' . date('H:i:s');
    }

    public function SetCheckOutAttribute($value)
    {
        $this->attributes['check_out'] = date('Y-m-d 11:00', strtotime($value));

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

    public function scopeFilter($query,  $filter)
    {
        $query->when($filter ?? false, fn ($query, $search) =>
        $query->where(
            fn ($query) => $query
                ->orWhere('id', 'Like', '%' . $search . '%')
                ->orWhere('type', 'Like', '%' . $search . '%')
                ->orWhereHas(
                    'customer',
                    fn ($query) =>
                    $query->Where('first_name', 'Like', '%' . $search . '%')
                        ->orWhere('last_name', 'Like', '%' . $search . '%')
                        ->orWhere('whatsapp', 'Like', '%' . $search . '%')
                        ->orWhere('contact_no', 'Like', '%' . $search . '%')
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
        ];
    }
}