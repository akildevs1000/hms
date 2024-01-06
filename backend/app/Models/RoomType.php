<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'description',
        'short_description',
        'pic',
        'projector_charges',
        'cleaning_charges',
        'electricity_charges',
        'audio_charges',
    ];

    public function room()
    {
        return $this->hasOne(Room::class);
    }
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function widget_available_rooms()
    {
        return $this->hasMany(Room::class)->where('online_available',   1)->where('status', 0);;
    }
    public function getPicAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/rooms/' . $value);
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
