<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Taxable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function CHeckInReport(Request $request)
    {
        $data = $this->CHeckInReportProcess($request);
        return Pdf::loadView('report.check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function CHeckInReportDownload(Request $request)
    {
        $data = $this->CHeckInReportProcess($request);
        return Pdf::loadView('report.check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function CHeckInReportProcess($request)
    {
        $company_id   = $request->company_id;
        $checkInModel = BookedRoom::query();
        return $checkInModel->clone()
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();
    }

    public function CHeckOutReport(Request $request)
    {
        $data = $this->CHeckOutReportDownload($request);
        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function CHeckOutReportDownload(Request $request)
    {
        $company_id          = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data                = $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function CHeckOutReportProcess($request)
    {
        $company_id    = $request->company_id;
        $checkOutModel = BookedRoom::query();
        return $checkOutModel->clone()->whereDate('check_out', $request->check_in)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 3);
                $q->where('company_id', $company_id);
            })->get();
    }

    public function availableRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        $model      = BookedRoom::query();
        $roomIds    = $model
            ->whereDate('check_in', '<=', $request->date)
            ->whereHas('booking', function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', '<=', $request->date);
            })
            ->with('booking')
            ->pluck('room_id');
        $data = Room::whereNotIn('id', $roomIds)->where('company_id', $company_id)->get();
        return $data;
    }

    public function availableRoomsReport(Request $request)
    {
        $data = $this->availableRoomsReportProcess($request);
        return Pdf::loadView('report.available_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function availableRoomsReportDownload(Request $request)
    {
        $data = $this->availableRoomsReportProcess($request);
        return Pdf::loadView('report.available_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function bookedRoomsReport(Request $request)
    {
        $data = $this->bookedRoomsReportProcess($request);

        return Pdf::loadView('report.booked_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function bookedRoomsReportDownload(Request $request)
    {
        $data = $this->bookedRoomsReportProcess($request);
        return Pdf::loadView('report.booked_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function bookedRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        $model      = BookedRoom::query();
        $roomIds    = $model
            ->whereDate('check_in', '<=', $request->date)
            ->whereHas('booking', function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', '<=', $request->date);
            })
            ->with('booking')
            ->pluck('room_id');

        return Room::whereIn('id', $roomIds)
            ->with('bookedRoom', function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', '<=', $request->date);
                $q->orderBy('id', 'ASC');
            })
            ->get();
    }

    public function paidRoomsReport(Request $request)
    {
        $data = $this->paidRoomsReportProcess($request);
        return Pdf::loadView('report.paid_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function paidRoomsReportDownload(Request $request)
    {
        $data = $this->paidRoomsReportProcess($request);
        return Pdf::loadView('report.paid_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function paidRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        return BookedRoom::whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '!=', 0);
            $q->where('company_id', $company_id);
        })->get();
    }

    public function dirtyRoomsReport(Request $request)
    {
        $data = $this->dirtyRoomsReportProcess($request);
        return Pdf::loadView('report.dirty_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function dirtyRoomsReportDownload(Request $request)
    {
        $data = $this->dirtyRoomsReportProcess($request);
        return Pdf::loadView('report.dirty_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function dirtyRoomsReportProcess($request)
    {
        $company_id        = $request->company_id;
        return $dirtyRooms = BookedRoom::whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 3);
            $q->where('company_id', $company_id);
        })->get();
    }

    // -----------------------

    public function expectCHeckInReport(Request $request)
    {
        $company_id         = $request->company_id;
        $expectCheckInModel = BookedRoom::query();
        $data               = $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 1);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function expectCHeckInReportDownload(Request $request)
    {
        $company_id         = $request->company_id;
        $expectCheckInModel = BookedRoom::query();
        $data               = $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 1);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function expectCHeckOutReport(Request $request)
    {
        $company_id          = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data                = $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function expectCHeckOutReportDownload(Request $request)
    {
        $company_id          = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data                = $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function gstInvoiceReportProcess($request)
    {
        $model = Taxable::query();

        $model->whereCompanyId($request->company_id)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            });

        if (($request->filled('search') && $request->search)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->where('gst_number', 'Like', '%' . $request->search . '%');
                $q->orWhere('reservation_no', 'Like', '%' . $request->search . '%');
            });
        }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->whereDate('check_in', '<=', $request->to);
                $q->WhereDate('check_out', '>=', $request->from);
            });
        }

        $model->with('booking.customer');
        $model->orderBy('id', 'asc');

        return $model->get();
    }

    public function gstInvoiceReport(Request $request)
    {
        $data = $this->gstInvoiceReportProcess($request);
        return Pdf::loadView('report.gst_invoice', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'landscape')
            ->stream();
    }

    public function gstInvoiceReportDownload(Request $request)
    {
        $data = $this->gstInvoiceReportProcess($request);
        return Pdf::loadView('report.gst_invoice', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'landscape')
            ->download();
    }

    public function incomingReportProcess($request)
    {
        $model = Payment::query();
        $model->where('company_id', $request->company_id);
        $model->whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', -1);
        });
        $model->with("booking:id,reservation_no");
        $model->orderByDesc("id");
        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->from;
            $to   = $request->to;
            $model->whereDate('created_at', '>=', $from);
            $model->whereDate('created_at', '<=', $to);
        }
        $totalIncomes = $this->TransactionsCounts($request);
        return Pdf::loadView('report.income', [
            'data'         => $model->get(),
            'request'      => $request,
            'totalIncomes' => $totalIncomes['income'],
            'accounts'     => $totalIncomes,
            'company'      => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function incomingReport(Request $request)
    {
        return $this->incomingReportProcess($request)->stream();
    }

    public function incomingReportDownload(Request $request)
    {
        return $this->incomingReportProcess($request)->download();
    }

    public function expenseReportProcess($request)
    {
        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $model->orderByDesc("id");
        $type = "";
        // if ($request->filled('from') && $request->filled('to')) {
        //     $from = $request->from;
        //     $to   = $request->to;
        //     $model->whereDate('created_at', '>=', $from);
        //     $model->whereDate('created_at', '<=', $to);
        // }

        if ($request->filled('from') && $request->filled('to')) {
            $model->whereDate('created_at', '>=', $request->from);
            $model->whereDate('created_at', '<=', $request->to);
            $model->orderBy("created_at", 'asc');
        } else {
            $model->orderBy("created_at", 'desc');
        }

        if ($request->is_management == 1 && $request->has('is_management') && $request->filled('is_management')) {
            $model->where('is_management', 1);
            $type = "Management";
        } else {
            $model->where('is_management', 0);
        }

        $totalExpenses = $this->TransactionsCounts($request);
        return Pdf::loadView('report.expense', [
            'data'          => $model->get(),
            'request'       => $request,
            'totalExpenses' => $totalExpenses['expense'],
            'accounts'      => $totalExpenses,
            'type'          => $type,
            'company'       => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function expenseReport(Request $request)
    {
        // return $this->expenseReportProcess($request);
        return $this->expenseReportProcess($request)->stream();
    }

    public function expenseReportDownload(Request $request)
    {
        return $this->expenseReportProcess($request)->download();
    }

    public function reservationReportProcess($request)
    {
        $status          = $request->r_type;
        $reservationTYpe = "";
        $model           = Booking::query()->latest()->filter(request('search'));

        if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_in', '>=', $request->from);
            $model->whereDate('check_in', '<=', $request->to);
        }

        if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_out', '>=', $request->from);
            $model->whereDate('check_out', '<=', $request->to);
        }

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', $request->source);
        }

        $model->orderBy('id', 'desc');

        switch ($status) {
            case 'up_coming_reservation_list':
                $model->where('booking_status', '=', 1);
                $reservationTYpe = 'UpComing';
                break;
            case 'check_out_reservation_list':
                $model->where(function ($q) {
                    $q->where('booking_status', '=', 3);
                    $q->orWhere('booking_status', '=', 0);
                });
                $reservationTYpe = 'Checkout';
                break;
            case 'in_house_reservation_list':
                $model->where('booking_status', '=', 2);
                $reservationTYpe = 'In House';
                break;
            default:
                abort(400, 'Invalid status');
        }

        $data = $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name,document',
            ])
            ->where('company_id', $request->company_id)
            ->get();

        return Pdf::loadView('report.reservation_list', [
            'data'            => $data,
            'reservationTYpe' => $reservationTYpe,
            'request'         => $request,
            'company'         => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function reservationReport(Request $request)
    {
        // return $this->reservationReportProcess($request);
        return $this->reservationReportProcess($request)->stream();
    }

    public function reservationReportDownload(Request $request)
    {
        return $this->reservationReportProcess($request)->download();
    }
}
