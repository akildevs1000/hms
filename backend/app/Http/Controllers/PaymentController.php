<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store($data)
    {
        $model = Payment::query();
        $data['date'] = date('Y-m-d');
        return $model->create($data);
    }

    public function update($data, $found)
    {
        return $found->update(['amount' => $data['amount']]);
    }

    public function index(Request $request)
    {
        $model = Payment::query();

        $model->where('company_id', $request->company_id);
        $model->whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', -1);
        });
        $model->with(['booking:id,reservation_no,customer_id' => ['customer:id,first_name,last_name']]);
        $model->orderByDesc("id");

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            $model->whereDate('date', '>=', $from);
            $model->whereDate('date', '<=', $to);
        }

        return $model->get();
    }
}
