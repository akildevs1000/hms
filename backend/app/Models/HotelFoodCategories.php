<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelFoodCategories extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $fillable = [

        'company_id',
        'name',
        'description',



    ];

    protected $appends = ['image'];
    public function items()
    {
        return $this->hasMany(HotelFoodItems::class, 'category_id');
    }
    public function getImageAttribute()
    {
        //public hotel
        //return asset('hotel/categories/' . $this->company_id . '/' . $this->id . '.jpg');

        $path = 'hotel/categories/' . $this->company_id . '/' . $this->id . '.jpg';
        return file_exists(public_path($path)) ? asset($path) : asset(('noimage.png'));
    }
}
