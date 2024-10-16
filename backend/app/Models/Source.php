<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Source extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d-M-y',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::lower($value);
        $this->attributes['slug'] = Str::of($value)->slug('_');
    }

    public function getNameAttribute($value)
    {
        return  ucfirst($value);
    }

    public function getTypeAttribute($value)
    {
        return  ucfirst($value);
    }

    protected static function boot()
    {
        parent::boot();

        // Order by name DESC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, "source_type", "type");
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
