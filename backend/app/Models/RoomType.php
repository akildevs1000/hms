<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $guarded = [];

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
