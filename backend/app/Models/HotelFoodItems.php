<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelFoodItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['category', "timings"];

    protected $fillable = [

        'company_id',
        'name',
        'timing_id',
        'category_id',
        'ingredients',
        'is_non_veg',
        'amount',
        'description',
        'status',




    ];
    protected $appends = ['item_picture'];

    public function getItemPictureAttribute()
    {
        //public hotel
        // return asset('hotel/food_menu/' . $this->company_id . '' . '/' . $this->id . '.jpg');

        $path = 'hotel/food_menu/' . $this->company_id . '/' . $this->id . '.jpg';
        return file_exists(public_path($path)) ? asset($path) : asset(('noimage.png'));
    }

    public function category()
    {
        return $this->hasOne(HotelFoodCategories::class,   "id", "category_id");
    }
    public function timings()
    {
        return $this->hasMany(HotelFoodItemsTimings::class,   "item_id")->orderBy('display_order', 'asc');
    }

    // public function timing()
    // {
    //     return $this->hasOne(HotelFoodTimings::class,  "id", "timing_id");
    // }
}
