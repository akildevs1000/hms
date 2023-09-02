<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ExpensesDocuments extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'expenses_documents';

    public function getFileNameAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/expenses_documents/' . $this->company_id . "/" . $value);
    }

    protected $casts = [
        'created_at' => 'datetime:d-M-y',
    ];

    protected static function boot()
    {
        parent::boot();

        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}
