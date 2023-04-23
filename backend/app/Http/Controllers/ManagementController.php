<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\OrderRoom;
use App\Models\Report;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function getOccupancyRate(Request $request)
    {
        $reportModel = Report::query();
        $reportModel->whereCompanyId($request->company_id);

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $reportModel->WhereDate('date', '>=', $request->from);
            $reportModel->whereDate('date', '<=', $request->to);
        }

        $totalSold   = $reportModel->sum('sold');
        $totalUnsold = $reportModel->sum('unsold');
        $data        = $reportModel->paginate($request->per_page ?? 30);

        return response()->json([
            'record'  => $data,
            'message' => '',
            'status'  => true,
        ]);
    }

    public function getSingleDayOccupancyRate(Request $request)
    {
        $reportModel = Report::query();
        $data        = $reportModel->whereCompanyId($request->company_id)->whereDate('date', $request->date)->first();
        return response()->json([
            'record'  => $data,
            'message' => '',
            'status'  => true,
        ]);
    }

    public function getOccupancyRateByMonth(Request $request)
    {
        $reportModel = Report::query();
        $arr         = [];
        $data        = $reportModel->select('sold', 'unsold', 'date')
            ->whereCompanyId($request->company_id)
            ->whereMonth('date', $request->month)
            ->get()->toArray();

        foreach ($data as $data) {
            $arr['date'][]   = $data['date'];
            $arr['sold'][]   = $data['sold'];
            $arr['unsold'][] = $data['unsold'];
        }
        return $arr;
    }

    public function getOccupancyRateByFilter(Request $request)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $reportModel = Report::query();
        $arr         = [];
        $reportModel->select('sold', 'unsold', 'date')->whereCompanyId($request->company_id);

        if ($request->filterType == 1) {
            $reportModel->whereDate('date', date('Y-m-d')); //today
        }

        if ($request->filterType == 2) {
            $reportModel->whereDate('date', date('Y-m-d', strtotime('-1 day'))); //Yesterday
        }

        if ($request->filterType == 3) {
            $reportModel->whereBetween('date', [$startOfWeek, $endOfWeek]); //This Week
        }

        if ($request->filterType == 4) {
            $reportModel->whereMonth('date', date('m')); //This month
        }

        if ($request->filterType == 5) {
            $reportModel->whereBetween('date', [$request->from, $request->to]); //custom
        }

        $data = $reportModel->orderBy('id', 'asc')->get()->toArray();

        foreach ($data as $data) {
            $arr['date'][]   = $data['date'];
            $arr['sold'][]   = $data['sold'];
            $arr['unsold'][] = $data['unsold'];
        }
        return $arr;
    }

    public function generateOccupancyRate(Request $request)
    {
        $orderRoomModel = OrderRoom::query();
        $roomModel      = Room::query();
        $totalRooms     = $roomModel->whereCompanyId($request->company_id)->count();
        $period         = CarbonPeriod::create($request->from, $request->to);
        foreach ($period as $date) {
            $ocuDate           = $date->format('Y-m-d');
            $numberOfSoldRooms = $orderRoomModel->clone()->whereDate('date', $ocuDate)
                ->whereCompanyId($request->company_id)->count();
            $soldRate   = round(($numberOfSoldRooms / $totalRooms) * 100);
            $unsoldRate = 100 - $soldRate;
            Report::whereDate('date', $date)->whereCompanyId($request->company_id)->delete();
            Report::create([
                'company_id' => $request->company_id,
                'date'       => $date,
                'sold'       => $soldRate,
                'unsold'     => $unsoldRate,
            ]);
        }
        return response()->json(['record' => null, 'message' => '', 'status' => true]);
    }

    public function generateOccupancyRateByBooking($request)
    {
        $date              = date('Y-m-d');
        $orderRoomModel    = OrderRoom::query();
        $roomModel         = Room::query();
        $totalRooms        = $roomModel->whereCompanyId($request->company_id)->count();
        $numberOfSoldRooms = $orderRoomModel->clone()->whereDate('date', $date)
            ->whereCompanyId($request->company_id)->count();
        $soldRate   = round(($numberOfSoldRooms / $totalRooms) * 100);
        $unsoldRate = 100 - $soldRate;
        Report::whereDate('date', $date)->whereCompanyId($request->company_id)->delete();
        Report::create([
            'company_id' => $request->company_id,
            'date'       => $date,
            'sold'       => $soldRate,
            'unsold'     => $unsoldRate,
        ]);
        return response()->json(['record' => null, 'message' => '', 'status' => true]);
    }

    public function getAuditReport(Request $request)
    {
        $model = Booking::query();

        $todayCheckin            = $this->todayCheckinAudit($model, $request);
        $continueRooms           = $this->continueAudit($model, $request);
        $todayCheckOut           = $this->todayCheckOutAudit($model, $request);
        $todayPayments           = $this->todayPaymentsAudit($model, $request);
        $cityLedgerPaymentsAudit = $this->cityLedgerPaymentsAudit($model, $request);

        $totExpense = Expense::whereCompanyId($request->company_id)
            ->where('is_management', 0)
            ->whereDate('created_at', $request->date)
            ->sum('total');

        return [
            'cityLedgerPaymentsAudit' => $cityLedgerPaymentsAudit,
            'todayCheckIn'            => $todayCheckin,
            'todayCheckOut'           => $todayCheckOut,
            'continueRooms'           => $continueRooms,
            'todayPayments'           => $todayPayments,
            'totExpense'              => number_format($totExpense, 2, '.', ''),
        ];
    }

    private function todayCheckinAudit($model, $request)
    {
        $company_id = $request->company_id;
        return $model
            ->where(function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', $request->date);
            })
            ->with('customer:id,first_name')
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')->with('transactions', function ($q) use ($request) {
            $q->where('is_posting', 0);
            // $q->where('credit', '>', 0);
            $q->whereDate('date', $request->date);
            $q->where('payment_method_id', '!=', 7);
            $q->where('company_id', $request->company_id)
                ->with('paymentMode');
        })->get();
    }

    private function continueAudit($model, $request)
    {
        $company_id           = $request->company_id;
        return $continueRooms = Booking::query()
            ->where(function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', '<', $request->date);
            })
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            })->get();
    }

    private function todayCheckOutAudit($model, $request)
    {
        $company_id           = $request->company_id;
        return $todayCheckOut = Booking::query()
            ->where(function ($q) use ($company_id, $request) {
                $q->whereIn('booking_status', [0, 3, 4]);
                $q->where('booking_status', '!=', -1);
                $q->where('company_id', $company_id);
                $q->whereDate('check_out', $request->date);
            })
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')
            ->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            })->get();
    }

    private function todayPaymentsAudit($model, $request)
    {
        $company_id           = $request->company_id;
        return $todayPayments = Booking::query()
            ->where(function ($q) use ($company_id, $request) {
                $q->whereIn('booking_status', [1]);
                $q->where('booking_status', '!=', -1);
                $q->where('paid_amounts', '>', 0);
                $q->where('company_id', $company_id);
            })
            ->whereHas('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('payment_method_id', '!=', 7);
            })
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')
            ->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            })->get();
    }

    private function cityLedgerPaymentsAudit($model, $request)
    {
        $company_id           = $request->company_id;
        return $todayPayments = Booking::query()
            ->where(function ($q) use ($company_id, $request) {
                $q->whereIn('booking_status', [0]);
                $q->where('booking_status', '!=', -1);
                $q->where('paid_amounts', '>', 0);
                $q->where('company_id', $company_id);
                $q->whereDate('check_out', '<', $request->date);
                // $q->whereDate('check_out', '!=', $request->date);
            })
            ->whereHas('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('payment_method_id', '!=', 7);
            })
            ->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            })
            ->get();
    }
}
