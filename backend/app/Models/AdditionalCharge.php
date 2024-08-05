<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalCharge extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the early check-in attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getEarlyCheckInAttribute($value)
    {
        // Customize the retrieval logic for early_check_in attribute
        // For example, you might want to format the value or perform some other logic
        return $value ?? 0;
    }

    /**
     * Get the late check-out attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getLateCheckOutAttribute($value)
    {
        // Customize the retrieval logic for late_check_out attribute
        return $value ?? 0;
    }

    /**
     * Get the extra bed attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getExtraBedAttribute($value)
    {
        // Customize the retrieval logic for extra_bed attribute
        return $value ?? 0;
    }
}
