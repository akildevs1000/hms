<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store($data)
    {
        $model = Payment::query();
        return  $model->create($data);
    }
}
