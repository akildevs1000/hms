<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpensesCategories extends Model
{
    use HasFactory;
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    protected $fillable = [

        'company_id',
        'name',
    ];
}
