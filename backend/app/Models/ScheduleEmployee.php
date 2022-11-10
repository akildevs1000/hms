<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEmployee extends Model
{
    use HasFactory;

    protected $with = ["shift", "shift_type", "logs", "first_log", "last_log"];

    protected $guarded = [];

    protected $casts = [
        'schedules' => 'array',
        'employee_ids' => 'array',
        'isOverTime' => 'boolean'
    ];

    /**
     * Get the shift that owns the ScheduleEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class)->withOut(["shift_type"]);
    }

    public function shift_type()
    {
        return $this->belongsTo(ShiftType::class, "shift_type_id")->withDefault([
            'name' => '---',
        ]);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, "employee_id", "system_user_id",);
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, "UserID", "employee_id")->orderBy("LogTime");
    }

    public function first_log()
    {
        return $this->hasOne(AttendanceLog::class, "UserID", "employee_id")->orderBy("LogTime");
    }

    public function last_log()
    {
        return $this->hasOne(AttendanceLog::class, "UserID", "employee_id")->orderByDesc("LogTime");
    }

    /**
     * Get all of the attendances for the ScheduleEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }

    // public function device()
    // {
    //     return $this->hasOneThrough(AttendanceLog::class, Device::class,"employee_id","device_id","id","id");
    // }


}
