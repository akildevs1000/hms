<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agent extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d-M-y',
    ];

    /**
     * Get the customer that owns the Agent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the booking that owns the Agent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // public function scopeFilter($query,  $filter)
    // {
    //     $query->when($filter ?? false, fn ($query, $search) =>
    //     $query->where(
    //         fn ($query) => $query
    //             ->orWhere('reservation_no', 'Like', '%' . $search . '%')
    //             ->orWhere('reference_no', 'Like', '%' . $search . '%')
    //             ->orWhere('type', 'Like', '%' . $search . '%')
    //             ->orWhereHas(
    //                 'customer',
    //                 fn ($query) =>
    //                 $query->Where('first_name', 'Like', '%' . $search . '%')
    //                     ->orWhere('last_name', 'Like', '%' . $search . '%')
    //                     ->orWhere('whatsapp', 'Like', '%' . $search . '%')
    //                     ->orWhere('contact_no', 'Like', '%' . $search . '%')
    //             )
    //     ));
    // }
}
