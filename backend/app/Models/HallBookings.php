<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallBookings extends Model
{
    use HasFactory;
    protected $table = 'hall_bookings';

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault([
            "name" => "---",
        ]);
    }
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function event()
    {
        return $this->belongsTo(EventTypes::class, 'event_type_id', 'id');
    }
    public function food()
    {
        return $this->hasMany(HallBookingFood::class, 'booking_id');
    }
    public function extraAmounts()
    {
        return $this->hasMany(HallBookingExtraAmounts::class, 'booking_id');
    }
    public function getDocumentAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/documents/booking/' . $value);
    }
    protected $fillable = [

        'customer_id',
        'room_id',
        'room_number',
        'reference_no',
        'reservation_no',
        'company_id',
        'booking_date',
        'booking_status',
        'customer_request',
        'source_type',
        'customer_type',
        'id_card_type',
        'id_card_no',
        'id_exp_date',
        'paid_by',
        'purpose',
        'cancel_reason',
        'user_id',
        'cancel_date',
        'cancel_date_time',
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_type',
        'event_pax',
        'event_special_setup',
        'event_audio_system',
        'event_projector',
        'event_stage_decoration',
        'hall_rent_amount',
        'hall_total_hours',
        'hall_rent_per_hour',
        'hall_electricity_amount',
        'hall_projector_amount',
        'hall_audio_system',
        'hall_cleaning_charges',
        'hall_tax_per',
        'hall_sgst_amount',
        'hall_cgst_amount',
        'food_total_amount',
        'food_tax_per',
        'food_cgst_amount',
        'food_sgst_amount',
        'inv_total_tax',
        'inv_total_without_tax',
        'inv_total',
        'hall_extra_amounts_total',
        'booking_id',
        'event_type_id',
        'discount',
    ];
}
