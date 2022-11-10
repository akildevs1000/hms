<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'company_id',
        'branch_id',
        'is_master',
        'first_login',
        'reset_password_code',
        'employee_role_id',
        'email_verified_at',
    ];

    protected $with = ['assigned_permissions'];

    public function assigned_permissions()
    {
        return $this->hasOne(AssignPermission::class, 'role_id', 'role_id');
    }

    public function assigned_employee_permissions()
    {
        return $this->hasOne(AssignPermission::class, 'role_id', 'employee_role_id');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d-M-y',
    ];

    // public function company()
    // {
    //     return $this->hasOne(Company::class);
    // }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Order by name DESC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}