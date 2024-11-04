<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sender()
    {
        return $this->belongsTo(Customer::class, 'sender_id');
    }

    public function lattest_room()
    {
        return $this->hasOneThrough(
            OrderRoom::class,
            Customer::class,
            'id', // Foreign key on the Customer table...
            'customer_id', // Foreign key on the OrderRoom table...
            'sender_id', // Local key on the Message table...
            'id' // Local key on the Customer table...
        )->select('customer_id', 'room_id', 'room_no');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function chat_photos()
    {
        return $this->hasMany(ChatPhoto::class);
    }

    // In the Message model or in your controller where a new message is created:
    public static function boot()
    {
        parent::boot();

        // Clear cache on message creation
        static::created(function ($message) {
            $companyId = $message->company_id;
            $cacheKey = "latest_three_messages_company_{$companyId}";
            Cache::forget($cacheKey);
        });
    }

    public function getVoiceNoteAttribute($value)
    {
        if (!$value) return null;
        return asset('voice_notes/' . $value);
    }
}
