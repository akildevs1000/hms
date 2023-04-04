<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Company;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Http\Controllers\Reports\ReportController;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function FilterCompanyList($model, $request, $model_name = null)
    {
        $model = $model::query();

        if (is_null($model_name)) {
            $model->when($request->company_id > 0, function ($q) use ($request) {
                return $q->where('company_id', $request->company_id);
            });

            $model->when(!$request->company_id, function ($q) use ($request) {
                return $q->where('company_id', 0);
            });
        }

        return $model;
    }

    public static function process($action, $job, $model, $id = null)
    {
        try {
            $m       = '\\App\\Models\\' . $model;
            $last_id = gettype($job) == 'object' ? $job->id : $id;

            $response = [
                'status'  => true,
                'record'  => $m::find($last_id),
                'message' => $model . ' has been ' . $action,
            ];

            if ($last_id) {
                return response()->json($response, 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'record'  => null,
                    'message' => $model . ' cannot ' . $action,
                ], 200);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function response($msg, $record, $status)
    {
        return response()->json(['record' => $record, 'message' => $msg, 'status' => $status], 200);
    }

    public function process_search($model, $input, $fields = [])
    {
        $model->where('id', 'LIKE', "%$input%");

        foreach ($fields as $key => $value) {
            if (is_string($value)) {
                $model->orWhere($value, 'LIKE', "%$input%");
            } else {
                foreach ($value as $relation_value) {
                    $model->orWhereHas($key, function ($query) use ($input, $relation_value) {
                        $query->where($relation_value, 'like', '%' . $input . '%');
                    });
                }
            }
        }
        return $model;
    }

    public function getStatusText($status)
    {
        $report_type = "Summary";

        if ($status == 'P') {
            $report_type = "Present";
        } else if ($status == 'A') {
            $report_type = "Absent";
        } else if ($status == '---') {
            $report_type = "Missing";
        } else if ($status == 'ME') {
            $report_type = "Manual Entry";
        }

        return $report_type;
    }




    public function getNumFormat($Num = null)
    {
        if (!$Num) {
            return "---";
        }

        return number_format($Num, 2);
    }

    public function getBookingModel($bookingId)
    {
        return Booking::find($bookingId);
    }

    public function TransactionsCounts($request)
    {

        $expense = Expense::query()
            ->where('company_id', $request->company_id)
            ->orderByDesc("id");

        $income = Payment::query()
            ->where('company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->orderByDesc("id");

        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->from;
            $to = $request->to;
            $expense->whereDate('created_at', '>=', $from);
            $expense->whereDate('created_at', '<=', $to);

            $income->whereDate('created_at', '>=', $from);
            $income->whereDate('created_at', '<=', $to);
        }

        $incomingWithoutCityLedger = $income->clone()->sum('amount') - $this->getSum($income, 7);
        $loss =   $incomingWithoutCityLedger - $income->clone()->sum('amount');
        $profit = $incomingWithoutCityLedger - $expense->clone()->sum('amount');

        return [
            'expense' => [
                'Cash' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 1))->sum('total'),
                'Card' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 2))->sum('total'),
                'Online' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 3))->sum('total'),
                'Bank' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 4))->sum('total'),
                'UPI' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 5))->sum('total'),
                'Cheque' => $expense->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', 6))->sum('total'),
                'OverallTotal' => $expense->clone()->sum('total'),
            ],
            'income' => [
                'Cash' => $this->getSum($income, 1),
                'Card' => $this->getSum($income, 2),
                'Online' => $this->getSum($income, 3),
                'Bank' => $this->getSum($income, 4),
                'UPI' => $this->getSum($income, 5),
                'Cheque' => $this->getSum($income, 6),
                'City_ledger' => $this->getSum($income, 7),
                'OverallTotal' => $incomingWithoutCityLedger,
            ],

            'profit' =>  $profit > 0 ? $profit  : 0 . '.00',
            'loss' =>  $loss > 0 ? $loss . '.00' : 0 . '.00',

            // 'profit' =>  $profit > 0 ? $profit . '.00' : 0 . '.00',
            // 'loss' =>  $loss > 0 ? $loss . '.00' : 0 . '.00',
        ];
    }

    public function getSum($model, $id)
    {
        return $model->clone()->whereHas('paymentMode', fn ($q) => $q->where('id', $id))->sum('amount');
    }

    public function getAmountFormat($amt = 0)
    {
        return number_format($amt, 2);
    }
}
