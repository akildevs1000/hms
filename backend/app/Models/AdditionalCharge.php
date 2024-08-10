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

    /**
     * Get the extra hour attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getExtraHourAttribute($value)
    {
        // Customize the retrieval logic for extra_hour attribute
        return $value ?? 0;
    }

    /**
     * Get the cleaning attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getCleaningAttribute($value)
    {
        // Customize the retrieval logic for cleaning attribute
        return $value ?? 0;
    }

    /**
     * Get the electricity attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getElectricityAttribute($value)
    {
        // Customize the retrieval logic for electricity attribute
        return $value ?? 0;
    }

    /**
     * Get the generator attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getGeneratorAttribute($value)
    {
        // Customize the retrieval logic for generator attribute
        return $value ?? 0;
    }

    /**
     * Get the audio attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getAudioAttribute($value)
    {
        // Customize the retrieval logic for audio attribute
        return $value ?? 0;
    }
}
