<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionNumberSeries extends Model
{
    use HasFactory;

    protected $table = "transaction_number_series";

    protected $guarded = [];

     protected $casts = [
        'json' => 'array',
    ];
}
