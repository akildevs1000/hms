<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelFoodItemsTimings extends Model
{
    use HasFactory;
    protected $table = 'hotel_food_item_timings';

    protected $with = ['timings'];
    protected $fillable = [

        'company_id',
        'item_id',
        'category_id',


    ];

    public function timings()
    {
        return $this->hasOne(HotelFoodTimings::class,   "id", "category_id");
    }
}
