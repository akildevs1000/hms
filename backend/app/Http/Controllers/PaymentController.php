<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentMode;
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
        $modes = PaymentMode::pluck("name")->map(fn($mode) => str_replace(' ', '', $mode))->toArray();
        $stats = [];

        foreach ($modes as $mode) {
            $stats[$mode] = 0;
        }

        $data = $this->getStats($request);

        foreach ($data as $item) {

            $payment_type =  $item->payment_type;

            $paymentTypeName = str_replace(' ', '', $payment_type->name) ?? "";

            foreach ($modes as $mode) {
                $item[$mode] = 0;
            }

            if ($payment_type && $paymentTypeName  && in_array($paymentTypeName, $modes)) {
                $item[$paymentTypeName] = $item->amount;
                $stats[$paymentTypeName] += $item->amount;
            }
        }

        $stats["total"] = array_sum($stats) - $stats["CityLedger"];

        return [
            "data" => $data,
            "stats" => $stats
        ];
    }


    public function getStats(Request $request)
    {
        $companyId = $request->input('company_id', 0);
        $searchKey = $request->input('search', null);
        $fromDate = $request->input('from_date', null);
        $toDate = $request->input('to_date', null);


        return Payment::query()
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
                'booking.customer:id,first_name,last_name',
                "payment_type"
            ])
            ->orderByDesc('id')
            ->get();
    }
}
