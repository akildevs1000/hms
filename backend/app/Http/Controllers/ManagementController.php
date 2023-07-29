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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
            ->whereMonth('date', $request->month)
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
            ->whereMonth('check_in', $request->month)
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
        setlocale(LC_MONETARY, 'en_IN');

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
            ->whereYear('created_at', $year)
            ->groupByRaw("EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date)")
            ->orderByRaw('year')
            ->orderByRaw('month')
            ->get()
            ->toArray();

        $incomeArray = Payment::selectRaw("EXTRACT(MONTH FROM date) as month")
            ->selectRaw("EXTRACT(YEAR FROM date) as year")
            ->selectRaw('sum(amount) as total')
            ->where('company_id', $request->company_id)
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

            $row['month'] = $monthArray[$month - 1]["text"] . ' ' . $year;
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
    public function getReportMonthlyWise(Request $request)
    {
        setlocale(LC_MONETARY, 'en_IN');
        setlocale(LC_MONETARY, 'en_IN');

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
                $row['month'] = $monthArray[$month]["text"] . ' ' . $year;

                $expensesModel = $model->clone()
                    ->whereMonth('created_at', $monthArray[$month]["value"])
                    ->whereYear('created_at', $year);

                $management_expenses = $expensesModel->clone()->where('is_management', 1)
                    ->sum('amount');

                $expenses = $expensesModel->clone()
                    ->where('is_management', 0)
                    ->sum('amount');

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
            ->whereYear('created_at', $year)
            ->when($request->filled('month'), function ($q) use ($request) {
                $q->whereMonth('created_at', $request->month);
            })
            ->limit(10)
            ->get();

        $total_price = Booking::
            where('company_id', $request->company_id)
        //->where('type', 'Walking')
            ->where('booking_status', "!=", -1)
            ->whereYear('created_at', $year)
            ->when($request->filled('month'), function ($q) use ($request) {
                $q->whereMonth('created_at', $request->month);
            })
            ->sum('total_price');

        $colorsArray = [["value" => "01", "text" => "Jan", "color" => "#3366CC"]
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
        return ["data" => $bookings, "colors" => $colorsArray, "total_price" => $total_price];
    }

    public function testcheckin(Request $request)
    {
        $model = Booking::query();

        $todayCheckin = $this->todayCheckinAudit($model, $request);

        return Pdf::loadView('report.audit.today_check_in', ['data' => $todayCheckin, 'company' => Company::find(1)])
            ->setPaper('a4', 'landscape')
            ->stream();

    }
}
