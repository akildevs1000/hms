<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use HasFactory;

    protected $guarded = [];

    const CASH = 1;
    const CARD = 2;
    const ONLINE = 3;
    const BANK = 4;
    const UPI = 5;
    const CHEQUE = 6;
    const CITYLEDGER = 7;

    protected $casts = ['created_at' => 'datetime:d-M-y'];
}
