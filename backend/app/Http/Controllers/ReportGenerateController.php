<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Company;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportGenerateController extends Controller
{
    public function generateAuditReport()
    {
        $company_ids = $this->getNotificationCompanyIds();
        $date = date('Y-m-d');
        //$date = date('Y-07-01');

        foreach ($company_ids as $company_id) {
            $model = Booking::query();
            echo $this->processData($company_id, $model, $date, 'Today Checkin Report', 1);
        }

        return true;
    }
    public function processData($company_id, $model, $date, $fileName = "", $reportType)
    {

        $request = array(
            'company_id' => $company_id,
            'date' => $date,
        );
        $request = (object) $request;

        $model = Booking::query();

        $todayCheckin = $this->todayCheckinAudit($model, $request);
        $continueRooms = $this->continueAudit($model, $request);
        $todayCheckOut = $this->todayCheckOutAudit($model, $request);
        $todayPayments = $this->todayPaymentsAudit($model, $request);
        $cityLedgerPaymentsAudit = $this->cityLedgerPaymentsAudit($model, $request);
        $cancelRooms = $this->cancelRooms($request);

        $foodOrderList = $this->getFoodOrderList($request);

        $totExpense = Expense::whereCompanyId($request->company_id)
            ->where('is_management', 0)
            ->whereDate('created_at', $date)
            ->sum('total');

        //if (count($todayCheckin) > 0)
        ///Today Check-in Report
        {
            $fileName = "Today Check-in Report";
            $pdf = Pdf::loadView('report.audit.today_check_in', ['data' => $todayCheckin, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])
                ->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            //Continue Report
            $fileName = "Continue Report";
            $pdf = Pdf::loadView('report.audit.continue_report', ['data' => $continueRooms, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])
                ->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            //Check-out Report
            $fileName = "Check-out Report";
            $pdf = Pdf::loadView('report.audit.check_out_report', ['data' => $todayCheckOut, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            //Today Booking Report
            $fileName = "Today Booking Report";
            $pdf = Pdf::loadView('report.audit.today_booking_report', ['data' => $todayPayments, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            //City Ledger Report
            $fileName = "City Ledger Report";
            $pdf = Pdf::loadView('report.audit.city_ledger_report', ['data' => $cityLedgerPaymentsAudit, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            //Cancel Rooms Report
            $fileName = "Cancel Rooms Report";
            $pdf = Pdf::loadView('report.audit.cancel_rooms', ['data' => $cancelRooms, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            //Food Order list
            $fileName = "Food Order list";
            $pdf = Pdf::loadView('report.audit.food_order_list', ['data' => $foodOrderList, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])->setPaper('a4', 'landscape')->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);

            return 'Reports are  generated successfully ' . $company_id . '.\n';
        }
        return ' data no found';
    }
    public function processData_old($company_id, $model, $date, $fileName = "", $reportType)
    {
        $model = Booking::query();
        switch ($reportType) {
            case 1:
                $todayCheckin = $this->todayCheckinAudit($model, $company_id, $date);
                break;
            default:
                break;
        }

        //if (count($todayCheckin) > 0)
        {
            $pdf = Pdf::loadView('report.audit.today_check_in', ['data' => $todayCheckin, 'company' => Company::find($company_id), 'fileName' => $fileName])
                ->setPaper('a4', 'landscape')
                ->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);

            return $fileName . ' generated successfully';
        }
        return ' data no found';
    }

    public function getNotificationCompanyIds()
    {
        return Company::orderBy('id', 'asc')->pluck("id");
    }

    // private function todayCheckinAudit($model, $company_id, $date)
    // {
    //     return $model
    //         ->where(function ($q) use ($company_id, $date) {
    //             $q->where('booking_status', '!=', 0);
    //             $q->where('booking_status', '!=', -1);
    //             $q->where('booking_status', '=', 2);
    //             $q->where('company_id', $company_id);
    //             $q->whereDate('check_in', $date);
    //         })
    //         ->with('customer:id,first_name')
    //         ->withSum(['transactions' => function ($q) use ($date) {
    //             $q->whereDate('date', $date);
    //         }], 'credit')->with('transactions', function ($q) use ($company_id, $date) {
    //         $q->where('is_posting', 0);
    //         $q->whereDate('date', $date);
    //         $q->where('payment_method_id', '!=', 7);
    //         $q->where('company_id', $company_id)
    //             ->with('paymentMode');
    //     })->get();
    // }

    private function getFoodOrderList($request)
    {
        $model = BookedRoom::query();
        $model->whereCompanyId($request->company_id);
        $model->whereDate('check_in', $request->date);
        $model->where(function ($q) {
            $q->where('tot_adult_food', '>', 0);
            $q->orWhere('tot_child_food', '>', 0);
        });
        $model->without('booking');
        $model->whereHas('booking', function ($q) use ($request) {
            $q->where('booking_status', 2);
            $q->whereCompanyId($request->company_id);
            $q->whereDate('check_in', $request->date);
        });
        $bookedRooms = $model->get();

        $tem = [];

        foreach ($bookedRooms as $bookedRoom) {

            $breakfast = explode('|', $bookedRoom->meal)[0];
            $lunch = explode('|', $bookedRoom->meal)[1];
            $dinner = explode('|', $bookedRoom->meal)[2];

            // if ($breakfast == '--- ' && $lunch == ' --- ' && $dinner == ' ---') {
            //     continue;
            // }

            if ($breakfast != '--- ') {
                $bookedRoom['breakfast'] = [
                    'title' => 'BreakFast',
                    'no_of_adult' => $bookedRoom->no_of_adult,
                    'no_of_child' => $bookedRoom->no_of_child,
                    'no_of_baby' => $bookedRoom->no_of_baby,
                ];
            }
            if ($lunch != ' --- ') {
                $bookedRoom['lunch'] = [
                    'title' => 'Lunch',
                    'no_of_adult' => $bookedRoom->no_of_adult,
                    'no_of_child' => $bookedRoom->no_of_child,
                    'no_of_baby' => $bookedRoom->no_of_baby,
                ];
            }

            if ($dinner != ' ---') {
                $bookedRoom['dinner'] = [
                    'title' => 'Dinner',
                    'no_of_adult' => $bookedRoom->no_of_adult,
                    'no_of_child' => $bookedRoom->no_of_child,
                    'no_of_baby' => $bookedRoom->no_of_baby,
                ];
            }

            $tem[] = $bookedRoom;
        }
        return ($tem);
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
            ->with('booking:id,reservation_no,created_at')
            ->get(['booking_id', 'room_no', 'room_type', 'grand_total', 'reason', 'cancel_by', 'created_at', 'action', 'check_in', 'status_before_cancelation', 'status_before_cancelation_msg']);
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
}
