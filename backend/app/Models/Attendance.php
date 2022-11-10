<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ["edit_date", "day"];

    protected $casts = [
        'date' => 'date',
    ];

    public function schedule()
    {
        return $this->belongsTo(ScheduleEmployee::class, "employee_id", "employee_id")->withOut("logs")->withDefault([
            "shift_type_id" => "---",
            "shift_type" => [
                "name" => "---",
            ],
        ]);
    }

    public function getDateAttribute($value)
    {
        return date("d-M-y", strtotime($value));
    }

    public function getDayAttribute()
    {
        return date("D", strtotime($this->date));
    }

    public function getOtAttribute($value)
    {
        $schedule = $this->schedule;

        if (!$schedule->isOverTime) {
            return "NA";
        }

        $working_hours = $schedule->shift->working_hours ?? null;
        $working_hours = $working_hours < 10 ? "0" . $working_hours : $working_hours;

        $starttimestamp = strtotime($working_hours . ":00");
        $endtimestamp   = strtotime($this->total_hrs);
        $maxtimestamp   = strtotime('06:00');
        $difference = abs($endtimestamp - $starttimestamp);

        if ($endtimestamp < $starttimestamp || $maxtimestamp > $endtimestamp) {
            return  "00:00";
        }

        return $this->getHrsMins($difference);
    }

    public function getHrsMins($difference)
    {
        $h = floor($difference / 3600);
        $h = $h < 0 ? "0" : $h;
        $m = floor($difference % 3600) / 60;
        $m = $m < 0 ? "0" : $m;

        return (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
    }

    public function getTotalHrsAttribute($value)
    {
        return strtotime($value) < strtotime('18:00') ? $value : '00:00';
    }

    /**
     * Get the user that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device_in()
    {
        return $this->belongsTo(Device::class, 'device_id_in');
    }

    /**
     * Get the user that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device_out()
    {
        return $this->belongsTo(Device::class, 'device_id_out')->withDefault([
            'name' => '---',
        ]);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, "employee_id", "system_user_id")->withOut("schedule")->withDefault([
            'first_name' => '---',
            "department" => [
                "name" => "---",
            ],
        ]);
    }

    public function employeeAttendance()
    {
        return $this->belongsTo(Employee::class, "employee_id");
    }

    public function getEditDateAttribute()
    {
        return date("Y-m-d", strtotime($this->date));
    }

    public function AttendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, "UserID", "employee_id");
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}
