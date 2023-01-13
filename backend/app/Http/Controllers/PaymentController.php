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

    public function update($data, $found)
    {
        return   $found->update(['amount' => $data['amount']]);
    }

    public function index(Request $request)
    {
        $model = Payment::query();

        $model->where('company_id', $request->company_id);
        $model->whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', -1);
        });
        $model->orderByDesc("id");

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            $model->whereDate('created_at', '>=', $from);
            $model->whereDate('created_at', '<=', $to);
        }

        return $model->paginate($request->per_page);
    }
}