<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Company;
use App\Models\BookedRoom;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $company_id = $request->company_id;
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
        $company_id = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data =  $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q)  use ($company_id) {
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
        $company_id = $request->company_id;
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
        $model   = BookedRoom::query();
        $roomIds = $model
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
        $data =   Room::whereNotIn('id', $roomIds)->where('company_id', $company_id)->get();
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
        $model   = BookedRoom::query();
        $roomIds = $model
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

        return  Room::whereIn('id', $roomIds)
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
        return BookedRoom::whereHas('booking', function ($q)  use ($company_id) {
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
        $company_id = $request->company_id;
        return  $dirtyRooms = BookedRoom::whereHas('booking', function ($q)  use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 3);
            $q->where('company_id', $company_id);
        })->get();
    }

    // -----------------------


    public function expectCHeckInReport(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();
        $data =  $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q)  use ($company_id) {
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
        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();
        $data =  $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q)  use ($company_id) {
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
        $company_id = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data =  $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q)  use ($company_id) {
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
        $company_id = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data =  $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q)  use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }
}
