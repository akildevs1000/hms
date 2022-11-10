<?php

namespace App\Models;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $with = ["schedule", "user", "department", "sub_department", "designation", "role", "first_log", "last_log"];

    protected $guarded = [];

    protected $casts = [
        'joining_date' => 'date:Y/m/d',
        'created_at' => 'datetime:d-M-y',
    ];

    protected $appends = ['show_joining_date', 'edit_joining_date', 'full_name', 'name_with_user_id'];

    public function schedule()
    {
        return $this->hasOne(ScheduleEmployee::class, "employee_id", "system_user_id")->withOut("logs")->withDefault([
            "shift_type_id" => "---",
            "shift_type" => [
                "name" => "---",
            ],
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class)->withDefault([
            "name" => "---",
        ]);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function sub_department()
    {
        return $this->belongsTo(SubDepartment::class);
    }

    public function getProfilePictureAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('media/employee/profile_picture/' . $value);
        // return asset(env('BUCKET_URL') . '/' . $value);

    }

    public function getCreatedAtAttribute($value): string
    {
        return date('d M Y', strtotime($value));
    }

    public function getShowJoiningDateAttribute(): string
    {
        return date('d M Y', strtotime($this->joining_date));
    }

    public function getEditJoiningDateAttribute(): string
    {
        return date('Y-m-d', strtotime($this->joining_date));
    }
    public function getFullNameAttribute(): string
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function getNameWithUserIDAttribute(): string
    {
        return $this->first_name . " - " . $this->employee_id;
    }

    // use Illuminate\Database\Eloquent\Builder;

    protected static function boot()
    {
        parent::boot();

        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, "employee_id", "employee_id");

    }

    public function logs()
    {
        return $this->hasOne(AttendanceLog::class, "UserID", "employee_id");
    }

    public function first_log()
    {
        return $this->logs()->orderBy("LogTime")->whereDate("LogTime", date("Y-m-d"));
    }

    public function last_log()
    {
        return $this->logs()->orderByDesc("LogTime")->whereDate("LogTime", date("Y-m-d"));
    }

    public function announcement()
    {
        return $this->belongsToMany(Announcement::class)->withTimestamps();
    }

    /**
     * The roles that belong to the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reportTo()
    {
        return $this->belongsToMany(Employee::class, 'employee_report', 'employee_id', 'report_id')->withTimestamps();
    }

    public function leave()
    {
        return $this->hasMany(Leave::class, 'employee_id', 'employee_id');
    }
}