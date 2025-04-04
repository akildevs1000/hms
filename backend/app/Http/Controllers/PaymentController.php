<?php

namespace App\Http\Controllers;

use App\Models\AdminExpense;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Transaction;
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

            $paymentTypeName = str_replace(' ', '', $payment_type->name ?? "") ?? "";

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
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
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

    public function ProfitLoss(Request $request)
    {
        $companyId = $request->company_id;
        $fromDate = $request->from_date ?? date("Y-m-d");
        $toDate = $request->to_date ?? $fromDate;

        // Single query to calculate income and city ledger totals
        $paymentTotals = Payment::query()
            ->selectRaw('
            SUM(CASE WHEN is_city_ledger = 0 THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN is_city_ledger = 1 THEN amount ELSE 0 END) as cityLedger
        ')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->first() ?? (object)['income' => 0, 'cityLedger' => 0]; // Handle no records case

        // Calculate total expenses
        $expenseTotals = AdminExpense::query()
            ->where('company_id', $companyId)
            ->where('status', AdminExpense::PAYMENT_STATUS_PAID)
            ->whereBetween('bill_date', [$fromDate, $toDate])
            ->sum('total') ?? 0;
        // Calculate profit or loss

        $finalTotal = $paymentTotals->income - $expenseTotals;

        return [
            'income' => $paymentTotals->income ?? 0,
            'cityLedger' => $paymentTotals->cityLedger ?? 0,
            'expense' => $expenseTotals ?? 0,
            'profit' => max($finalTotal, 0),  // Profit if positive
            'loss' => min($finalTotal, 0),    // Loss if negative
        ];
    }

    public function Payments(Request $request)
    {

        $model = Transaction::query();


        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereBetween('date', [$request->from, $request->to]);
        }


        // Optional search value
        $search = $request->search;

        if ($search) {
            $model->where(function ($q) use ($search) {
                $q->orWhereHas('booking', function ($bookingQuery) use ($search) {
                    $bookingQuery->where('id', $search)
                        ->orWhere('reservation_no', $search);
                });
            });
        }


        return $model
            ->with("paymentMode", "booking")
            ->where('company_id', $request->company_id)
            ->where('credit', ">", 0)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 20);
    }
}
