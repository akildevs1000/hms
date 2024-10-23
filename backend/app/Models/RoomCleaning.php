<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomCleaning extends Model
{
    use HasFactory;

    const DIRTY = "Dirty";
    const CLEANED = "Cleaned";

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function cleaned_by_user()
    {
        return $this->belongsTo(User::class, "cleaned_by_user_id");
    }

    public function response_by_user()
    {
        return $this->belongsTo(User::class, "response_by_user_id");
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }
}
