<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminExpenseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tax' => 'integer',
    ];

    /**
     * Get the user that owns the AdminExpenseItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense()
    {
        return $this->belongsTo(AdminExpense::class,"admin_expense_id")->with("payment");
    }
    
}
