<?php

namespace App\Http\Controllers;

use App\Console\Commands\AuditReport;
use App\Models\AuditHistory;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Company;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportGenerateController extends Controller
{
    public function generateAuditReport()
    {
        $company_ids = $this->getNotificationCompanyIds();
        $date = date('Y-m-d', strtotime('yesterday')); // Use yesterday's date
        //$date = date('Y-07-01');

        foreach ($company_ids as $company_id) {
            $model = Booking::query();
            echo $this->processData($company_id, $model, $date, 'Today Checkin Report', 1);
        }

        return true;
    }
    public function processData($company_id, $date)
    {

        $request = array(
            'company_id' => $company_id,
            'date' => $date,
        );
        $request = (object) $request;

        $todayCheckin = $this->todayCheckinAudit($request);
        $continueRooms = $this->continueAudit($request);
        $todayCheckOut = $this->todayCheckOutAudit($request);
        $todayPayments = $this->todayPaymentsAudit($request);
        $cityLedgerPaymentsAudit = $this->cityLedgerPaymentsAudit($request);
        $cancelRooms = $this->cancelRooms($request);

        $foodOrderList = $this->getFoodOrderList($request);

        $totExpense = Expense::whereCompanyId($request->company_id)
            ->where('is_management', 0)
            ->whereDate('created_at', $date)
            ->sum('total');

        $arr = [];

        AuditHistory::where("company_id", $company_id)->whereDate("created_at", $date)->delete();

        $this->processPayload("check_in", "Today Check-in Report", $date, $company_id, $todayCheckin, "today_check_in");
        $this->processPayload("continue", "Continue Report", $date, $company_id, $continueRooms, "continue_report");
        $this->processPayload("check_out", "Check-out Report", $date, $company_id, $todayCheckOut, "check_out_report");
        $this->processPayload("payment", "Today Booking Report", $date, $company_id, $todayPayments, "today_booking_report");
        $this->processPayload("cityLedger", "City Ledger Report", $date, $company_id, $cityLedgerPaymentsAudit, "city_ledger_report");
        $this->processPayload("cancel", "Cancel Rooms Report", $date, $company_id, $cancelRooms, "cancel_rooms");
        $this->processPayload("food", "Food Order list", $date, $company_id, $foodOrderList, "food_order_list");

        $arr = [
            "type" => "expense",
            "file_name" => "---",
            "file_path" => "---",
            'data' => number_format($totExpense, 2, '.', ''),
            'company_id' => $company_id,
            'dateTime' => date("d M y h:i:s"),
        ];

        AuditHistory::create($arr);


        return 'Reports are  generated successfully ' . $company_id . '.\n';
    }

    public function processPayload($type, $fileName, $date, $company_id, $data, $bladeView)
    {

        $pdf = Pdf::loadView('report.audit.' . $bladeView, ['data' => $data, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])
            ->setPaper('a4', 'landscape')->output();
        $file_path  = "pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf';
        $arr = [
            "type" => $type,
            "file_name" => $fileName,
            "file_path" => $file_path,
            'data' => $data,
            'company_id' => $company_id,
            'dateTime' => date("d M y h:i:s"),
        ];

        AuditHistory::create($arr);
        Storage::disk('local')->put($file_path, $pdf);
    }
    public function getNotificationCompanyIds()
    {
        return Company::orderBy('id', 'asc')->pluck("id");
    }

    private function todayCheckinAudit($request)
    {
        $company_id = $request->company_id;
        return
            Booking::where(function ($q) use ($company_id, $request) {
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
            });
    }

    private function continueAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
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
            });
    }

    private function todayCheckOutAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
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
            });
    }

    private function todayPaymentsAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
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
            });
    }

    private function cancelRooms($request)
    {
        $company_id = $request->company_id;
        return CancelRoom::query()
            ->with('user')
            ->whereDate('created_at', $request->date)
            ->where('company_id', $company_id)
            ->with('booking:id,reservation_no,created_at');
        //->get(['booking_id', 'room_no', 'room_type', 'grand_total', 'reason', 'cancel_by', 'created_at', 'action', 'check_in', 'status_before_cancelation', 'status_before_cancelation_msg']);
    }

    private function cityLedgerPaymentsAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
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
            });
    }


    public function processSummaryData($company_id, $date)
    {

        $request = array(
            'company_id' => $company_id,
            'date' => $date,
        );
        $request = (object) $request;


        $todayCheckin = $this->todayCheckinAudit($request);
        $continueRooms = $this->continueAudit($request);
        $todayCheckOut = $this->todayCheckOutAudit($request);
        $todayPayments = $this->todayPaymentsAudit($request);
        $cityLedgerPaymentsAudit = $this->cityLedgerPaymentsAudit($request);
        $cancelRooms = $this->cancelRooms($request);

        return $found = DB::table('booked_rooms')
        ->where("company_id", $company_id)
        ->where(function ($query) use ($date) {
            $query->whereDate('check_out', $date)
                ->orWhereDate('check_in', $date);
        })
        ->selectRaw('SUM(breakfast * no_of_adult) as breakfast, SUM(lunch) as lunch, SUM(dinner) as dinner')
        ->first();

    return [
        "breakfast" => $found->breakfast ?? 0,
        "lunch" => $found->lunch ??  0,
        "dinner" => $found->dinner ??  0
    ];

        return $foodOrderList = (new FoodController)->index($request);

        $totExpense = Expense::whereCompanyId($request->company_id)
            ->where('is_management', 0)
            ->whereDate('created_at', $date)
            ->sum('total');

        return  [
            'todayCheckin' => $todayCheckin->count(),
            'continueRooms' => $continueRooms->count(),
            'todayCheckOut' => $todayCheckOut->count(),
            'todayPayments' => $todayPayments->count(),
            'cityLedgerPaymentsAudit' => $cityLedgerPaymentsAudit->count(),
            'cancelRooms' => $cancelRooms->count(),

        ];
    }
}
