<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'otp',
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
        'is_verified',
    ];

    protected $appends = [
        'cash_sum',
        'card_sum',
        'online_sum',
        'bank_sum',
        'UPI_sum',
        'cheque_sum',
        'City_ledger_sum',
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

    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function GetCashSumAttribute()
    {

        return  $this->getSumByModel($this->id, 1);
    }

    public function GetCardSumAttribute()
    {
        return $this->getSumByModel($this->id, 2);
    }

    public function GetOnlineSumAttribute()
    {
        return $this->getSumByModel($this->id, 3);
    }

    public function GetBankSumAttribute()
    {
        return $this->getSumByModel($this->id, 4);
    }

    public function GetUPISumAttribute()
    {
        return $this->getSumByModel($this->id, 5);
    }

    public function GetChequeSumAttribute()
    {
        return   $this->getSumByModel($this->id, 6);
    }

    public function GetCityLedgerSumAttribute()
    {
        return   $this->getSumByModel($this->id, 7, 'debit');
    }

    public function getSumByModel($userId = null, $id = null, $col = 'credit')
    {
        return Transaction::where('user_id', $userId)
            ->whereDate('created_at', '>=',  request('from_date', date('Y-m-d')))
            ->whereDate('created_at', '<=', request('to_date', date('Y-m-d')))
            ->whereHas('paymentMode', fn ($q) => $q->where('id', $id))->sum($col) ?? 0;
    }
}
