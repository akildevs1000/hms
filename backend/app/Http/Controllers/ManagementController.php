<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Company;
use App\Models\Expense;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Room;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $totalSold = $reportModel->sum('sold');
        $totalUnsold = $reportModel->sum('unsold');
        $data = $reportModel->paginate($request->per_page ?? 30);

        return response()->json([
            'record' => $data,
            'message' => '',
            'status' => true,
        ]);
    }

    public function getSingleDayOccupancyRate(Request $request)
    {
        $reportModel = Report::query();
        $data = $reportModel->whereCompanyId($request->company_id)->whereDate('date', $request->date)->first();
        return response()->json([
            'record' => $data,
            'message' => '',
            'status' => true,
        ]);
    }

    public function getOccupancyRateByMonth(Request $request)
    {
        $reportModel = Report::query();
        $arr = [];
        $data = $reportModel->select('sold', 'unsold', 'date')
            ->whereCompanyId($request->company_id)
        // ->whereMonth('date', $request->month)
            ->whereBetween('date', [$request->filter_from_date, $request->filter_to_date])
            ->orderBy('date', 'ASC')
            ->get()->toArray();

        foreach ($data as $data) {
            $arr['date'][] = $data['date'];
            $arr['sold'][] = $data['sold'];
            $arr['unsold'][] = $data['unsold'];
        }
        return $arr;
    }

    public function getSourceRateByMonth(Request $request)
    {

        $data = Booking::whereCompanyId($request->company_id)
        //->whereMonth('check_in', $request->month)
            ->whereBetween('created_at', [$request->filter_from_date, $request->filter_to_date])
            ->where('booking_status', '!=', -1)
            ->select('source', 'total_price')
            ->get()
            ->groupBy('source')
            ->map(function ($group) {
                return $group->sum('total_price');
            });

        return $data;

        return 'gg';

        $reportModel = Report::query();
        $arr = [];
        $data = $reportModel->select('sold', 'unsold', 'date')
            ->whereCompanyId($request->company_id)
            ->whereMonth('date', $request->month)
            ->get()->toArray();

        foreach ($data as $data) {
            $arr['date'][] = $data['date'];
            $arr['sold'][] = $data['sold'];
            $arr['unsold'][] = $data['unsold'];
        }
        return $arr;
    }

    public function getOccupancyRateByFilter(Request $request)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $reportModel = Report::query();
        $arr = [];
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
            $arr['date'][] = $data['date'];
            $arr['sold'][] = $data['sold'];
            $arr['unsold'][] = $data['unsold'];
        }
        return $arr;
    }

    public function generateOccupancyRate(Request $request)
    {
        $orderRoomModel = OrderRoom::query();
        $roomModel = Room::query();
        $totalRooms = $roomModel->whereCompanyId($request->company_id)->count();
        $period = CarbonPeriod::create($request->from, $request->to);
        foreach ($period as $date) {
            $ocuDate = $date->format('Y-m-d');
            $numberOfSoldRooms = $orderRoomModel->clone()->whereDate('date', $ocuDate)
                ->whereCompanyId($request->company_id)->count();
            $soldRate = round(($numberOfSoldRooms / $totalRooms) * 100);
            $unsoldRate = 100 - $soldRate;
            Report::whereDate('date', $date)->whereCompanyId($request->company_id)->delete();
            Report::create([
                'company_id' => $request->company_id,
                'date' => $date,
                'sold' => $soldRate,
                'unsold' => $unsoldRate,
                'sold_qty' => $numberOfSoldRooms,
                'unsold_qty' => $totalRooms - $numberOfSoldRooms,
            ]);
        }
        return response()->json(['record' => null, 'message' => '', 'status' => true]);
    }

    public function generateOccupancyRateByBooking($request)
    {
        $date = date('Y-m-d');
        $orderRoomModel = OrderRoom::query();
        $roomModel = Room::query();
        $totalRooms = $roomModel->whereCompanyId($request->company_id)->count();
        $numberOfSoldRooms = $orderRoomModel->clone()->whereDate('date', $date)
            ->whereCompanyId($request->company_id)->count();
        $soldRate = round(($numberOfSoldRooms / $totalRooms) * 100);
        $unsoldRate = 100 - $soldRate;

        Report::whereDate('date', $date)->whereCompanyId($request->company_id)->delete();
        Report::create([
            'company_id' => $request->company_id,
            'date' => $date,
            'sold' => $soldRate,
            'unsold' => $unsoldRate,
            'sold_qty' => $numberOfSoldRooms,
            'unsold_qty' => $totalRooms - $numberOfSoldRooms,
        ]);
        return response()->json(['record' => null, 'message' => '', 'status' => true]);
    }

    public function getAuditReport(Request $request)
    {

        $model = Booking::query();

        $todayCheckin = $this->todayCheckinAudit($model, $request);
        $continueRooms = $this->continueAudit($model, $request);
        $todayCheckOut = $this->todayCheckOutAudit($model, $request);
        $todayPayments = $this->todayPaymentsAudit($model, $request);
        $cityLedgerPaymentsAudit = $this->cityLedgerPaymentsAudit($model, $request);
        $cancelRooms = $this->cancelRooms($request);

        $totExpense = Expense::whereCompanyId($request->company_id)
            ->where('is_management', 0)
            ->whereDate('created_at', $request->date)
            ->sum('total');

        return [
            'cityLedgerPaymentsAudit' => $cityLedgerPaymentsAudit,
            'todayCheckIn' => $todayCheckin,
            'todayCheckOut' => $todayCheckOut,
            'continueRooms' => $continueRooms,
            'todayPayments' => $todayPayments,
            'cancelRooms' => $cancelRooms,

            'totExpense' => number_format($totExpense, 2, '.', ''),
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
        $company_id = $request->company_id;
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
        $company_id = $request->company_id;
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
        $company_id = $request->company_id;
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

    private function cancelRooms($request)
    {
        $company_id = $request->company_id;
        return CancelRoom::query()
            ->with('user')
            ->whereDate('created_at', $request->date)
            ->where('company_id', $company_id)
            ->with('booking:id,reservation_no')
            ->get(['booking_id', 'room_no', 'room_type', 'grand_total', 'reason', 'cancel_by', 'created_at', 'action', 'check_in']);
    }

    private function cityLedgerPaymentsAudit($model, $request)
    {
        $company_id = $request->company_id;
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
    public function getReportMonthlyWiseGroup(Request $request)
    {
        // session(['isMonthReport' => ])

        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $returnArray = [];
        //$year = $request->year;
        $year_number = '';
        $filter_from_date = $request->filter_from_date;
        $filter_to_date = $request->filter_to_date;
        if (count(explode('-', $request->filter_from_date)) == 1) {
            $filter_from_date = $filter_from_date . '-01';
            $filter_to_date = $filter_to_date . '-' . cal_days_in_month(CAL_GREGORIAN, explode('-', $filter_to_date)[1], explode('-', $filter_to_date)[0]);

            $year_number = date('Y', strtotime($filter_from_date));
        } else {

        }

        $soldArray = Report::selectRaw("EXTRACT(MONTH FROM date) as month")
            ->selectRaw("EXTRACT(YEAR FROM date) as year")
            ->selectRaw('sum(sold_qty) as total')
            ->where('company_id', $request->company_id)
        // ->whereYear('created_at', $year)
            ->whereBetween('date', [$filter_from_date, $filter_to_date])

            ->groupByRaw("EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()
            ->toArray();

        $incomeArray = Transaction::
            selectRaw("EXTRACT(MONTH FROM date) as month")
            ->selectRaw("EXTRACT(YEAR FROM date) as year")
            ->selectRaw('sum(credit) as total')
            ->where('company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
        // ->whereYear('created_at', $year)
            ->whereBetween('date', [$filter_from_date . ' 00:00:00', $filter_to_date . ' 23:59:59'])

            ->groupByRaw("EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()->toArray();

        $management_expensesArray = Expense::selectRaw("EXTRACT(MONTH FROM created_at) as month")
            ->selectRaw("EXTRACT(YEAR FROM created_at) as year")
            ->selectRaw('sum(total) as total')
            ->where('company_id', $request->company_id)

            ->whereBetween('created_at', [$filter_from_date . ' 00:00:00', $filter_to_date . ' 23:59:59'])

            ->where('is_management', 1)
            ->groupByRaw("EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()->toArray();

        $expensesArray = Expense::selectRaw("EXTRACT(MONTH FROM created_at) as month")
            ->selectRaw("EXTRACT(YEAR FROM created_at) as year")
            ->selectRaw('sum(total) as total')
            ->where('company_id', $request->company_id)
        // ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$filter_from_date . ' 00:00:00', $filter_to_date . ' 23:59:59'])

            ->where('is_management', 0)
            ->groupByRaw("EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()->toArray();

        $totalRooms = 0;
        $totalIncome = 0;
        $totalExpenses = 0;
        $totalManagementExpenses = 0;
        $totalProfit = 0;
        $totalPercentage = 0;
        $totalArray = [];

        $startDate = new DateTime($filter_from_date);
        $endDate = new DateTime($filter_to_date);

        for ($date = $startDate; $date <= $endDate; $date->modify('+1 month')) {

            $year = $date->format('Y');
            $monthStr = $date->format('M');
            $month = $date->format('m');

            $sold = 0;
            $income = 0;
            $expenses = 0;
            $management_expenses = 0;

            //$monthStr = $month;
            $row = [];

            $row['month'] = $monthStr . ' ' . $year; // $monthArray[$month - 1]["text"]; // . ' ' . $year;
            $row['sold'] = 0;
            $soldTemp = array_filter($soldArray, function ($result) use ($month, $year) {
                if ($result['month'] == $month && $result['year'] == $year) {
                    return $result;
                }
                ;
            });
            $incomeTemp = array_filter($incomeArray, function ($result) use ($month, $year) {
                if ($result['month'] == $month && $result['year'] == $year) {
                    return $result;
                }
                ;
            });
            $managementExpensesTemp = array_filter($management_expensesArray, function ($result) use ($month, $year) {
                if ($result['month'] == $month && $result['year'] == $year) {
                    return $result;
                }
                ;
            });
            $ExpensesTemp = array_filter($expensesArray, function ($result) use ($month, $year) {
                if ($result['month'] == $month && $result['year'] == $year) {
                    return $result;
                }
                ;
            });
            if (!empty($soldTemp)) {
                $sold = reset($soldTemp)["total"];
            }

            if (!empty($incomeTemp)) {
                $income = reset($incomeTemp)["total"];
            }

            if (!empty($managementExpensesTemp)) {
                $management_expenses = reset($managementExpensesTemp)["total"];
            }

            if (!empty($ExpensesTemp)) {
                $expenses = reset($ExpensesTemp)["total"];
            }
            if (!empty($managementExpensesTemp)) {
                $management_expenses = reset($managementExpensesTemp)["total"];
            }

            $income = round($income, 2);
            $expenses = round($expenses, 2);
            $management_expenses = round($management_expenses, 2);
            $row['month_number'] = $month;
            $row['year_number'] = $year;
            $row['sold'] = $sold;
            $row['management_expenses'] = $this->getMoneyFormat($management_expenses);
            $row['expenses'] = $this->getMoneyFormat($expenses);
            //$row['color'] = $monthArray[$month - 1]["color"];
            $row['income'] = $this->getMoneyFormat($income); //$income;
            $profit = $income - ($management_expenses + $expenses);
            $profit = round($profit, 2);
            $row['profit'] = $this->getMoneyFormat($profit); //number_format($profit, 2);
            $percentage = $income > 0 ? ($profit / $income) * 100 : 0;
            $row['percentage'] = round($percentage);
            $row['total_expenses'] = $this->getMoneyFormat($management_expenses + $expenses);

            $returnArray[] = $row;

            $totalRooms += $sold;
            $totalIncome += $income;
            $totalExpenses += $expenses;
            $totalManagementExpenses += $management_expenses;
            $totalProfit += $profit;
        }
        $totalPercentage += $totalIncome > 0 ? ($totalProfit / $totalIncome) * 100 : 0;

        $totalArray['totalRooms'] = $totalRooms;
        $totalArray['totalIncome'] = $this->getMoneyFormat($totalIncome);
        $totalArray['totalExpenses'] = $this->getMoneyFormat($totalExpenses);
        $totalArray['totalManagementExpenses'] = $this->getMoneyFormat($totalManagementExpenses);
        $totalArray['totalProfit'] = $this->getMoneyFormat($totalProfit);
        $totalArray['totalPercentage'] = round($totalPercentage);

        return ["data" => $returnArray, "grandTotal" => $totalArray];
    }
    public function getReportMonthlyWiseGroup_old(Request $request)
    {

        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $returnArray = [];
        $year = $request->year;

        $monthArray = [["value" => "01", "text" => "Jan", "color" => "#3366CC"]
            , ["value" => "02", "text" => "Feb", "color" => "#FF69B4"]
            , ["value" => "03", "text" => "Mar", "color" => "#00FF00"]
            , ["value" => "04", "text" => "Apr", "color" => "#FFD700"]
            , ["value" => "05", "text" => "May", "color" => "#FF4500"]
            , ["value" => "06", "text" => "Jun", "color" => "#800080"]
            , ["value" => "07", "text" => "Jul", "color" => "#FF6347"]
            , ["value" => "08", "text" => "Aug", "color" => "#008080"]
            , ["value" => "09", "text" => "Sep", "color" => "#FFA500"]
            , ["value" => "10", "text" => "Oct", "color" => "#DC143C"]
            , ["value" => "11", "text" => "Nov", "color" => "#7CFC00"]
            , ["value" => "12", "text" => "Dec", "color" => "#4169E1"]];

        $soldArray = Report::selectRaw("EXTRACT(MONTH FROM date) as month")
            ->selectRaw("EXTRACT(YEAR FROM date) as year")
            ->selectRaw('sum(sold) as total')
            ->where('company_id', $request->company_id)
            ->whereYear('date', $year)
            ->groupByRaw("EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()
            ->toArray();

        $incomeArray = Payment::selectRaw("EXTRACT(MONTH FROM date) as month")
            ->selectRaw("EXTRACT(YEAR FROM date) as year")
            ->selectRaw('sum(amount) as total')
            ->where('company_id', $request->company_id)
            ->where('payment_mode', "!=", 7)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->whereYear('created_at', $year)
            ->groupByRaw("EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()->toArray();

        $management_expensesArray = Expense::selectRaw("EXTRACT(MONTH FROM created_at) as month")
            ->selectRaw("EXTRACT(YEAR FROM created_at) as year")
            ->selectRaw('sum(amount) as total')
            ->where('company_id', $request->company_id)
            ->whereYear('created_at', $year)
            ->where('is_management', 1)
            ->groupByRaw("EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()->toArray();

        $expensesArray = Expense::selectRaw("EXTRACT(MONTH FROM created_at) as month")
            ->selectRaw("EXTRACT(YEAR FROM created_at) as year")
            ->selectRaw('sum(amount) as total')
            ->where('company_id', $request->company_id)
            ->whereYear('created_at', $year)
            ->where('is_management', 0)
            ->groupByRaw("EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()->toArray();

        $totalRooms = 0;
        $totalIncome = 0;
        $totalExpenses = 0;
        $totalManagementExpenses = 0;
        $totalProfit = 0;
        $totalPercentage = 0;
        $totalArray = [];
        for ($month = 1; $month <= 12; $month++) {

            $sold = 0;
            $income = 0;
            $expenses = 0;
            $management_expenses = 0;

            $monthStr = $month;
            $row = [];

            $row['month'] = $monthArray[$month - 1]["text"]; // . ' ' . $year;
            $row['sold'] = 0;
            $soldTemp = array_filter($soldArray, function ($result) use ($monthStr) {
                if ($result['month'] == $monthStr) {
                    return $result;
                }
                ;
            });
            $incomeTemp = array_filter($incomeArray, function ($result) use ($monthStr) {
                if ($result['month'] == $monthStr) {
                    return $result;
                }
                ;
            });
            $managementExpensesTemp = array_filter($management_expensesArray, function ($result) use ($monthStr) {
                if ($result['month'] == $monthStr) {
                    return $result;
                }
                ;
            });
            $ExpensesTemp = array_filter($expensesArray, function ($result) use ($monthStr) {

                if ($result['month'] == $monthStr) {
                    return $result;
                }
                ;
            });
            if (!empty($soldTemp)) {
                $sold = reset($soldTemp)["total"];
            }

            if (!empty($incomeTemp)) {
                $income = reset($incomeTemp)["total"];
            }

            if (!empty($managementExpensesTemp)) {
                $management_expenses = reset($managementExpensesTemp)["total"];
            }

            if (!empty($ExpensesTemp)) {
                $expenses = reset($ExpensesTemp)["total"];
            }
            if (!empty($managementExpensesTemp)) {
                $management_expenses = reset($managementExpensesTemp)["total"];
            }

            $income = round($income, 2);
            $expenses = round($expenses, 2);
            $management_expenses = round($management_expenses, 2);
            $row['month_number'] = $month;
            $row['sold'] = $sold;
            $row['management_expenses'] = $this->getMoneyFormat($management_expenses);
            $row['expenses'] = $this->getMoneyFormat($expenses);
            $row['color'] = $monthArray[$month - 1]["color"];
            $row['income'] = $this->getMoneyFormat($income); //$income;
            $profit = $income - ($management_expenses + $expenses);
            $profit = round($profit, 2);
            $row['profit'] = $this->getMoneyFormat($profit); //number_format($profit, 2);
            $percentage = $income > 0 ? ($profit / $income) * 100 : 0;
            $row['percentage'] = round($percentage);
            $row['total_expenses'] = $this->getMoneyFormat($management_expenses + $expenses);

            $returnArray[] = $row;

            $totalRooms += $sold;
            $totalIncome += $income;
            $totalExpenses += $expenses;
            $totalManagementExpenses += $management_expenses;
            $totalProfit += $profit;
        }
        $totalPercentage += $totalIncome > 0 ? ($totalProfit / $totalIncome) * 100 : 0;

        $totalArray['totalRooms'] = $totalRooms;
        $totalArray['totalIncome'] = $this->getMoneyFormat($totalIncome);
        $totalArray['totalExpenses'] = $this->getMoneyFormat($totalExpenses);
        $totalArray['totalManagementExpenses'] = $this->getMoneyFormat($totalManagementExpenses);
        $totalArray['totalProfit'] = $this->getMoneyFormat($totalProfit);
        $totalArray['totalPercentage'] = round($totalPercentage);

        return ["data" => $returnArray, "grandTotal" => $totalArray];
    }

    public function getReportMonthlyWiseGroupPrint(Request $request)
    {
        //$data = $this->getReportDailyWiseGroup($request);

        return Pdf::loadView('report.revenue_report_monthlywise', [

            'company' => Company::find($request->company_id)])->setPaper('a4', 'landscape');
    }
    public function getReportMonthlyWise(Request $request)
    {

        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $returnArray = [];
        $year = $request->year;

        $monthArray = [
            ["value" => "01", "text" => "Jan", "color" => "#3366CC"]
            , ["value" => "02", "text" => "Feb", "color" => "#FF69B4"]
            , ["value" => "03", "text" => "Mar", "color" => "#00FF00"]
            , ["value" => "04", "text" => "Apr", "color" => "#FFD700"]
            , ["value" => "05", "text" => "May", "color" => "#FF4500"]
            , ["value" => "06", "text" => "Jun", "color" => "#800080"]
            , ["value" => "07", "text" => "Jul", "color" => "#FF6347"]
            , ["value" => "08", "text" => "Aug", "color" => "#008080"]
            , ["value" => "09", "text" => "Sep", "color" => "#FFA500"]
            , ["value" => "10", "text" => "Oct", "color" => "#DC143C"]
            , ["value" => "11", "text" => "Nov", "color" => "#7CFC00"]
            , ["value" => "12", "text" => "Dec", "color" => "#4169E1"]];

        $totalRooms = 0;
        $totalIncome = 0;
        $totalExpenses = 0;
        $totalManagementExpenses = 0;
        $totalProfit = 0;
        $totalPercentage = 0;
        $totalArray = [];
        //group
        for ($month = 0; $month <= 11; $month++) {

            if (isset($monthArray[$month])) {
                $row = [];
                // $row['month'] = $monthArray[$month]["text"] . ' ' . $year;

                $expensesModel = $model->clone()
                    ->whereMonth('created_at', $monthArray[$month]["value"])
                    ->whereYear('created_at', $year);

                $management_expenses = $expensesModel->clone()->where('is_management', 1)
                    ->sum('total');

                $expenses = $expensesModel->clone()
                    ->where('is_management', 0)
                    ->sum('total');

                $reportModel = Report::query();

                $sold = $reportModel
                    ->whereCompanyId($request->company_id)
                    ->whereMonth('date', $monthArray[$month]["value"])
                    ->whereYear('date', $year)
                    ->sum('sold');

                $income = Payment::query() //transaction
                    ->where('company_id', $request->company_id)
                    ->whereHas('booking', function ($q) {
                        $q->where('booking_status', '!=', -1);
                    })
                    ->whereMonth('date', $monthArray[$month]["value"])
                    ->whereYear('date', $year)
                    ->sum('amount');

                $income = round($income, 2);
                $expenses = round($expenses, 2);
                $management_expenses = round($management_expenses, 2);

                $row['month_number'] = $month;
                $row['sold'] = $sold;
                $row['management_expenses'] = $this->getMoneyFormat($management_expenses);
                $row['expenses'] = $this->getMoneyFormat($expenses);
                $row['color'] = $monthArray[$month]["color"];
                $row['income'] = $this->getMoneyFormat($income); //$income;
                $profit = $income - ($management_expenses + $expenses);
                $profit = round($profit, 2);
                $row['profit'] = $this->getMoneyFormat($profit); //number_format($profit, 2);
                $percentage = $income > 0 ? ($profit / $income) * 100 : 0;
                $row['percentage'] = round($percentage);
                $row['total_expenses'] = $this->getMoneyFormat($management_expenses + $expenses);

                //$row['colors'] = $monthArray;
                $returnArray[] = $row;
                $totalRooms += $sold;
                $totalIncome += $income;
                $totalExpenses += $expenses;
                $totalManagementExpenses += $management_expenses;
                $totalProfit += $profit;

            }
        }
        $totalPercentage += $totalIncome > 0 ? ($totalProfit / $totalIncome) * 100 : 0;

        $totalArray['totalRooms'] = $totalRooms;
        $totalArray['totalIncome'] = $this->getMoneyFormat($totalIncome);
        $totalArray['totalExpenses'] = $this->getMoneyFormat($totalExpenses);
        $totalArray['totalManagementExpenses'] = $this->getMoneyFormat($totalManagementExpenses);
        $totalArray['totalProfit'] = $this->getMoneyFormat($totalProfit);
        $totalArray['totalPercentage'] = round($totalPercentage);

        return ["data" => $returnArray, "grandTotal" => $totalArray];
    }
    private function getMoneyFormat($amount)
    {

        $append = '';
        if (floor($amount) == $amount) {
            $append = '.00';
        } else {
            $append = '';
        }

        //$amount = round($amount);
        $amount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $amount . $append);

        return $amount;
    }
    public function getReportTop10Customers(Request $request)
    {
        $year = $request->year;

        $bookings = Booking::select(
            DB::raw("string_agg(rooms, ',') as rooms"),
            'bookings.customer_id',
            DB::raw('sum(total_price) as customer_total_price'),
            DB::raw('count(id) as number_of_visits'),
        )
            ->with('customer:id,contact_no')
            ->groupBy('bookings.customer_id')
            ->orderByDesc('customer_total_price')
            ->where('company_id', $request->company_id)
        //->where('type', 'Walking')
        // -1 cancel booking ;
            ->where('booking_status', "!=", -1)
        // ->whereYear('created_at', $year)
        // ->when($request->filled('month'), function ($q) use ($request) {
        //     $q->whereMonth('created_at', $request->month);
        // })
            ->whereBetween('check_in', [$request->filter_from_date . ' 00:00:00', $request->filter_to_date . ' 23:59:59'])
            ->limit(10)
            ->get();

        // $total_price = Booking::where('company_id', $request->company_id)
        // //->where('type', 'Walking')
        //     ->where('booking_status', "!=", -1)
        //     ->whereYear('created_at', $year)
        //     ->when($request->filled('month'), function ($q) use ($request) {
        //         $q->whereMonth('created_at', $request->month);
        //     })
        //     ->sum('total_price');
        $total_price = Payment::query() //transaction
            ->where('company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->whereBetween('date', [$request->filter_from_date, $request->filter_to_date])
            ->sum('amount');

        return ["data" => $bookings, "colors" => null, "total_price" => $total_price];
    }

    public function testcheckin(Request $request)
    {
        $model = Booking::query();

        $todayCheckin = $this->todayCheckinAudit($model, $request);

        return Pdf::loadView('report.audit.today_check_in', ['data' => $todayCheckin, 'company' => Company::find(1)])
            ->setPaper('a4', 'landscape')
            ->stream();

    }
    public function getReportDailyWiseGroup(Request $request)
    {

        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $returnArray = [];
        $year = $request->year;

        $dayColorsArray = [];
        // $colors = ["#3366CC", "#FF69B4", "#00FF00", "#FFD700", "#FF4500", "#800080", "#FF6347", "#008080", "#FFA500", "#DC143C", "#7CFC00", "#4169E1", "#FF1493", "#32CD32", "#FFD700", "#4682B4", "#800000", "#808000", "#FF4500", "#DA70D6", "#808080", "#2E8B57", "#BA55D3", "#ADFF2F", "#20B2AA", "#FF4500", "#87CEEB", "#3CB371", "#FA8072", "#9370DB", "#6A5ACD", "#00FA9A", "#FF69B4"];
        $dayColorsArray = ["#3366CC"];

        // for ($i = 1; $i <= 31; $i++) {
        //     $dayColorsArray[$i] = $colors[$i - 1];
        // }

        $totalRooms = 0;
        $totalIncome = 0;
        $totalExpenses = 0;
        $totalManagementExpenses = 0;
        $totalProfit = 0;
        $totalPercentage = 0;
        $totalArray = [];
        //group

        $expencesModel = Expense::selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, EXTRACT(DAY  FROM created_at) as date,  SUM(amount) as total_amount')
            ->where('company_id', $request->company_id)
        // ->whereMonth('created_at', $request->month)
        // ->whereYear('created_at', $request->year)
            ->whereBetween('created_at', [$request->filter_from_date . ' 00:00:00', $request->filter_to_date . ' 23:59:59'])

            ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at) , EXTRACT(DAY FROM created_at)  '))
            ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'), 'asc')
            ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'), 'asc')
            ->orderBy(DB::raw('EXTRACT(DAY  FROM created_at)'), 'asc')

        ;

        $ExpencesManagementArray = $expencesModel->clone()->where('is_management', 1)->get()->toArray();
        $ExpencesNonManagementArray = $expencesModel->clone()->where('is_management', 0)->get()->toArray();

        $soldArray = Report::selectRaw('EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY  FROM date) as date ,sold_qty')
            ->whereCompanyId($request->company_id)
        // ->whereMonth('created_at', $request->month)
        // ->whereYear('created_at', $request->year)
            ->whereBetween('date', [$request->filter_from_date, $request->filter_to_date])
            ->orderBy('date', 'ASC')->get()->toArray();

        $incomeArray = Payment::selectRaw('EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY  FROM date) as date,  SUM(amount) as total_amount')
            ->where('company_id', $request->company_id)
            ->where('payment_mode', "!=", 7)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
        // ->whereMonth('created_at', $request->month)
        // ->whereYear('created_at', $request->year)
            ->whereBetween('date', [$request->filter_from_date, $request->filter_to_date])
            ->groupBy(DB::raw('year, month , date  '))->orderBy('date', 'ASC')->get()->toArray();
        $startTimestamp = strtotime($request->filter_from_date);
        $endTimestamp = strtotime($request->filter_to_date);

        for ($currentTimestamp = $startTimestamp; $currentTimestamp <= $endTimestamp; $currentTimestamp += 86400) {
            $year = date('Y', $currentTimestamp);
            $month = date('m', $currentTimestamp);
            $day = date('d', $currentTimestamp);

            $income = 0;
            $expenses = 0;
            $management_expenses = 0;
            $sold = 0;
            $income = array_filter($incomeArray, function ($item) use ($request, $year, $month, $day) {
                return $item['year'] == $year && $item['month'] == $month && $item['date'] == $day;
            });

            $management_expenses = array_filter($ExpencesManagementArray, function ($item) use ($request, $year, $month, $day) {
                return $item['year'] == $year && $item['month'] == $month && $item['date'] == $day;
            });
            $expenses = array_filter($ExpencesNonManagementArray, function ($item) use ($request, $year, $month, $day) {
                return $item['year'] == $year && $item['month'] == $month && $item['date'] == $day;
            });
            $sold = array_filter($soldArray, function ($item) use ($request, $year, $month, $day) {

                $format = $request->year . '-' . $request->month . '-' . $day;
                return $item['year'] == $year && $item['month'] == $month && $item['date'] == $day;
            });

            if (!empty($income)) {
                $income = reset($income)["total_amount"];
            } else {
                $income = 0;
            }
            if (!empty($management_expenses)) {
                $management_expenses = reset($management_expenses)["total_amount"];
            } else {
                $management_expenses = 0;
            }
            if (!empty($expenses)) {
                $expenses = reset($expenses)["total_amount"];
            } else {
                $expenses = 0;
            }
            if (!empty($sold)) {
                $sold = reset($sold)["sold_qty"];
            } else {
                $sold = 0;
            }

            $color = $dayColorsArray[0]; // $dayColorsArray[$day];

            // $income = isset($income[0]) ? $income[0]['total_amount'] : 0;

            // $expenses = isset($expenses[0]) ? $expenses[0]['total_amount'] : 0;

            // $management_expenses = isset($management_expenses[0]) ? $management_expenses[0]['total_amount'] : 0;

            // $sold = isset($sold[0]) ? $sold[0]['sold'] : 0;

            $income = round($income, 2);
            $expenses = round($expenses, 2);
            $management_expenses = round($management_expenses, 2);
            $row['month'] = $month;
            $row['day'] = $day; // $request->year . '-' . $request->month . '-' . $day;
            $row['date'] = date('Y-m-d', strtotime($year . '-' . $month . '-' . $day));
            $row['sold'] = $sold;
            $row['management_expenses'] = $this->getMoneyFormat($management_expenses);
            $row['expenses'] = $this->getMoneyFormat($expenses);
            $row['color'] = $color;
            $row['income'] = $this->getMoneyFormat($income); //$income;
            $profit = $income - ($management_expenses + $expenses);
            $profit = round($profit, 2);
            $row['profit'] = $this->getMoneyFormat($profit); //number_format($profit, 2);
            $percentage = $income > 0 ? ($profit / $income) * 100 : 0;
            $percentage = $percentage < 0 ? 0 : $percentage;

            $row['percentage'] = round($percentage);
            $row['total_expenses'] = $this->getMoneyFormat($management_expenses + $expenses);

            //$row['colors'] = $monthArray;
            $returnArray[] = $row;
            $totalRooms += $sold;
            $totalIncome += $income;
            $totalExpenses += $expenses;
            $totalManagementExpenses += $management_expenses;
            $totalProfit += $profit;
        }

        $totalPercentage += $totalIncome > 0 ? ($totalProfit / $totalIncome) * 100 : 0;

        $totalArray['totalRooms'] = $totalRooms;
        $totalArray['totalIncome'] = $this->getMoneyFormat($totalIncome);
        $totalArray['totalExpenses'] = $this->getMoneyFormat($totalExpenses);
        $totalArray['totalManagementExpenses'] = $this->getMoneyFormat($totalManagementExpenses);
        $totalArray['totalProfit'] = $this->getMoneyFormat($totalProfit);
        $totalArray['totalPercentage'] = round($totalPercentage);

        return ["data" => $returnArray, "grandTotal" => $totalArray];
    }
    public function getReportDailyWiseGroup_old(Request $request)
    {

        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $returnArray = [];
        $year = $request->year;

        $dayColorsArray = [];
        // $colors = ["#3366CC", "#FF69B4", "#00FF00", "#FFD700", "#FF4500", "#800080", "#FF6347", "#008080", "#FFA500", "#DC143C", "#7CFC00", "#4169E1", "#FF1493", "#32CD32", "#FFD700", "#4682B4", "#800000", "#808000", "#FF4500", "#DA70D6", "#808080", "#2E8B57", "#BA55D3", "#ADFF2F", "#20B2AA", "#FF4500", "#87CEEB", "#3CB371", "#FA8072", "#9370DB", "#6A5ACD", "#00FA9A", "#FF69B4"];
        $dayColorsArray = ["#3366CC"];

        // for ($i = 1; $i <= 31; $i++) {
        //     $dayColorsArray[$i] = $colors[$i - 1];
        // }

        $totalRooms = 0;
        $totalIncome = 0;
        $totalExpenses = 0;
        $totalManagementExpenses = 0;
        $totalProfit = 0;
        $totalPercentage = 0;
        $totalArray = [];
        //group

        $expencesModel = Expense::selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, EXTRACT(DAY  FROM created_at) as date,  SUM(amount) as total_amount')
            ->where('company_id', $request->company_id)
            ->whereMonth('created_at', $request->month)
            ->whereYear('created_at', $request->year)
            ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at) , EXTRACT(DAY FROM created_at)  '))
            ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'), 'asc')
            ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'), 'asc')
            ->orderBy(DB::raw('EXTRACT(DAY  FROM created_at)'), 'asc')

        ;

        $ExpencesManagementArray = $expencesModel->clone()->where('is_management', 1)->get()->toArray();
        $ExpencesNonManagementArray = $expencesModel->clone()->where('is_management', 0)->get()->toArray();

        $soldArray = Report::selectRaw('EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY  FROM date) as date ,sold')
            ->whereCompanyId($request->company_id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->orderBy('date', 'ASC')->get()->toArray();

        $incomeArray = Payment::selectRaw('EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY  FROM date) as date,  SUM(amount) as total_amount')
            ->where('company_id', $request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw('year, month , date  '))->get()->toArray();

        for ($day = 1; $day <= 31; $day++) {

            $income = 0;
            $expenses = 0;
            $management_expenses = 0;
            $sold = 0;

            $income = array_filter($incomeArray, function ($item) use ($request, $day) {
                return $item['year'] == $request->year && $item['month'] == $request->month && $item['date'] == $day;
            });

            $management_expenses = array_filter($ExpencesManagementArray, function ($item) use ($request, $day) {
                return $item['year'] == $request->year && $item['month'] == $request->month && $item['date'] == $day;
            });
            $expenses = array_filter($ExpencesNonManagementArray, function ($item) use ($request, $day) {
                return $item['year'] == $request->year && $item['month'] == $request->month && $item['date'] == $day;
            });
            $sold = array_filter($soldArray, function ($item) use ($request, $day) {

                $format = $request->year . '-' . $request->month . '-' . $day;
                return $item['year'] == $request->year && $item['month'] == $request->month && $item['date'] == $day;
            });

            if (!empty($income)) {
                $income = reset($income)["total_amount"];
            } else {
                $income = 0;
            }
            if (!empty($management_expenses)) {
                $management_expenses = reset($management_expenses)["total_amount"];
            } else {
                $management_expenses = 0;
            }
            if (!empty($expenses)) {
                $expenses = reset($expenses)["total_amount"];
            } else {
                $expenses = 0;
            }
            if (!empty($sold)) {
                $sold = reset($sold)["sold"];
            } else {
                $sold = 0;
            }

            $color = $dayColorsArray[0]; // $dayColorsArray[$day];

            // $income = isset($income[0]) ? $income[0]['total_amount'] : 0;

            // $expenses = isset($expenses[0]) ? $expenses[0]['total_amount'] : 0;

            // $management_expenses = isset($management_expenses[0]) ? $management_expenses[0]['total_amount'] : 0;

            // $sold = isset($sold[0]) ? $sold[0]['sold'] : 0;

            $income = round($income, 2);
            $expenses = round($expenses, 2);
            $management_expenses = round($management_expenses, 2);
            $row['month'] = $day; // $request->year . '-' . $request->month . '-' . $day;
            $row['date'] = date('Y-m-d', strtotime($request->year . '-' . $request->month . '-' . $day));
            $row['sold'] = $sold;
            $row['management_expenses'] = $this->getMoneyFormat($management_expenses);
            $row['expenses'] = $this->getMoneyFormat($expenses);
            $row['color'] = $color;
            $row['income'] = $this->getMoneyFormat($income); //$income;
            $profit = $income - ($management_expenses + $expenses);
            $profit = round($profit, 2);
            $row['profit'] = $this->getMoneyFormat($profit); //number_format($profit, 2);
            $percentage = $income > 0 ? ($profit / $income) * 100 : 0;
            $percentage = $percentage < 0 ? 0 : $percentage;

            $row['percentage'] = round($percentage);
            $row['total_expenses'] = $this->getMoneyFormat($management_expenses + $expenses);

            //$row['colors'] = $monthArray;
            $returnArray[] = $row;
            $totalRooms += $sold;
            $totalIncome += $income;
            $totalExpenses += $expenses;
            $totalManagementExpenses += $management_expenses;
            $totalProfit += $profit;
        }

        $totalPercentage += $totalIncome > 0 ? ($totalProfit / $totalIncome) * 100 : 0;

        $totalArray['totalRooms'] = $totalRooms;
        $totalArray['totalIncome'] = $this->getMoneyFormat($totalIncome);
        $totalArray['totalExpenses'] = $this->getMoneyFormat($totalExpenses);
        $totalArray['totalManagementExpenses'] = $this->getMoneyFormat($totalManagementExpenses);
        $totalArray['totalProfit'] = $this->getMoneyFormat($totalProfit);
        $totalArray['totalPercentage'] = round($totalPercentage);

        return ["data" => $returnArray, "grandTotal" => $totalArray];
    }
}
