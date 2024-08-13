<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
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
        $companyId = $request->input('company_id', 0);
        $searchKey = $request->input('search', null);
        $fromDate = $request->input('from_date', null);
        $toDate = $request->input('to_date', null);
    
        $model = Payment::query()
            ->where('company_id', $companyId)
            ->when($searchKey, function ($query) use ($searchKey, $companyId) {
                $query->whereHas('booking', function ($q) use ($searchKey, $companyId) {
                    $q->where('company_id', $companyId)
                      ->where('booking_status', '!=', -1)
                      ->where(function ($query) use ($searchKey) {
                          $query->where('reservation_no', 'like', "%{$searchKey}%")
                                ->orWhere('room', 'like', "%{$searchKey}%")
                                ->orWhereHas('customer', function (Builder $customerQuery) use ($searchKey) {
                                    $customerQuery->where('first_name', 'like', "%{$searchKey}%")
                                                  ->orWhere('last_name', 'like', "%{$searchKey}%")
                                                  ->orWhere('contact_no', 'like', "%{$searchKey}%");
                                });
                      });
                });
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('date', [$fromDate, $toDate]);
            })
            ->with([
                'booking:id,reservation_no,customer_id',
                'booking.customer:id,first_name,last_name'
            ])
            ->orderByDesc('id')
            ->get();
    
        return $model;
    }
    
}
