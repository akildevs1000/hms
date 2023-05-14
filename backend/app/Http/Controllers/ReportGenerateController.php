<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Company;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportGenerateController extends Controller
{
    public function generateAuditReport()
    {
        $company_ids = $this->getNotificationCompanyIds();
        $date = date('Y-m-d');
        // $date = date('Y-m-13');

        foreach ($company_ids as $company_id) {
            $model = Booking::query();
            $this->processData($company_id, $model, $date, 'Today Checkin Report', 1);
        }

        return true;
    }


    public function processData($company_id, $model, $date, $fileName = "", $reportType)
    {
        $model = Booking::query();
        switch ($reportType) {
            case 1:
                $todayCheckin = $this->todayCheckinAudit($model, $company_id, $date);
                break;
            default:
                break;
        }

        if (count($todayCheckin) > 0) {
            $pdf =  Pdf::loadView('report.audit.today_check_in', ['data' => $todayCheckin, 'company' => Company::find($company_id), 'fileName' => $fileName])
                ->setPaper('a4', 'landscape')
                ->output();
            Storage::disk('local')->put("pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf', $pdf);
            return $fileName  . ' generated successfully';
        }
        return  ' data no found';
    }

    public function getNotificationCompanyIds()
    {
        return Company::orderBy('id', 'asc')->pluck("id");
    }

    private function todayCheckinAudit($model, $company_id, $date)
    {
        return $model
            ->where(function ($q) use ($company_id, $date) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', $date);
            })
            ->with('customer:id,first_name')
            ->withSum(['transactions' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }], 'credit')->with('transactions', function ($q) use ($company_id, $date) {
                $q->where('is_posting', 0);
                $q->whereDate('date', $date);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $company_id)
                    ->with('paymentMode');
            })->get();
    }
}
