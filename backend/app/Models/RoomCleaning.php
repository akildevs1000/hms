<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomCleaning extends Model
{
    use HasFactory;

    const DIRTY   = "Dirty";
    const CLEANED = "Cleaned";
    const NEUTRAL = "Neutral";
    const CLEANING_IN_PROGRESS = "Cleaning In Progress";

    protected $guarded = [];

    protected $appends = ["last_cleaned_at"];


    protected $casts = [
        'attachments' => 'array',
    ];

    public function getLastCleanedAtAttribute()
    {
        $date = $this->created_at ? Carbon::parse($this->created_at)->format('d-M-Y') : null;
        $endTime = $this->end_time ?? null;
        return trim($date . ' ' . $endTime);
    }

    // Convert attachment filenames to full URLs
    public function getAttachmentsAttribute($value)
    {
        if (! $value) {
            return [];
        }

        $attachments = json_decode($value, true);

        return array_map(fn($file) => asset('attachments/' . $file), $attachments);
    }

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

    public function getBeforeAttachmentAttribute($value)
    {
        if (! $value) {
            return null;
        }

        return asset('before_attachments/' . $value);
    }

    public function getAfterAttachmentAttribute($value)
    {
        if (! $value) {
            return null;
        }

        return asset('after_attachments/' . $value);
    }

    public function getVoiceNoteAttribute($value)
    {
        if (! $value) {
            return null;
        }

        return asset('voice_notes/' . $value);
    }

    public function getMaintenanceVoiceNoteAttribute($value)
    {
        if (! $value) {
            return null;
        }

        return asset('maintenance_voice_notes/' . $value);
    }
}
