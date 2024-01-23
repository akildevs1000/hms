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

    public function getImageAttribute()
    {
        return asset('storage/hotel/categories/' . $this->company_id . '/' . $this->id . '.jpg');
    }
}
