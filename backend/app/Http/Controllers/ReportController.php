<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\BookedRoom;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function CHeckInReport(Request $request)
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

    public function CHeckInReportDownload(Request $request)
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


    public function CHeckOutReport(Request $request)
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
}
