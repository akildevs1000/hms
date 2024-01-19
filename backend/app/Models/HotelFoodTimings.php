<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelFoodTimings extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $fillable = [

        'company_id',
        'name',
        'start_hour',
        'end_hour',



    ];

    // public function categoy()
    // {
    //     return $this->hasOne(HotelFoodItems::class,   "timing_id", "id");
    // }
}
