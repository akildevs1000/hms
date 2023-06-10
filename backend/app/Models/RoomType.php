<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'weekday_price',
        // 'weekend_price',
        // 'holiday_price',
        // 'company_id',
        'name',
        'price',
        'room_only_price',
        'Break_fast_price',
        'Break_fast_with_dinner_price',
        'Break_fast_with_lunch_price',
        'lunch_with_dinner_price',
        'full_board_price',
        'max_person',
        'created_at',
        'updated_at',
        'company_id',
        'adult',
        'child',
        'baby',
        'holiday_price',
        'weekday_price',
        'weekend_price',
    ];

    public function room()
    {
        return $this->hasOne(Room::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}
