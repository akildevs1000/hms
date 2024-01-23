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
    protected $appends = ['image'];

    public function getImageAttribute()
    {
        return asset('storage/hotel/timings/' . $this->company_id . '/' . $this->id . '.jpg');
    }
    // public function categoy()
    // {
    //     return $this->hasOne(HotelFoodItems::class,   "timing_id", "id");
    // }
}
