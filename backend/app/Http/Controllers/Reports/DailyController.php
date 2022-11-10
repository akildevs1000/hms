<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ReportNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DailyController extends Controller
{
    public function generateSummaryReport()
    {
        $company_ids = ReportNotification::distinct("company_id")->pluck("company_id");

        foreach ($company_ids as $company_id) {
            return $this->report($company_id, "Summary", "daily_summary.pdf");
        }

        return true;
    }

    public function generatePresentReport()
    {
        $company_ids = ReportNotification::distinct("company_id")->pluck("company_id");

        foreach ($company_ids as $company_id) {
            return $this->report($company_id, "Present", "daily_present.pdf", "P");
        }

        return true;
    }

    public function generateAbsentReport()
    {
        $company_ids = ReportNotification::distinct("company_id")->pluck("company_id");

        foreach ($company_ids as $company_id) {
            $this->report($company_id, "Absent", "daily_absent.pdf", "A");
        }

        return true;
    }

    public function generateMissingReport()
    {
        $company_ids = ReportNotification::distinct("company_id")->pluck("company_id");

        foreach ($company_ids as $company_id) {
            $this->report($company_id, "Missing", "daily_missing.pdf", "---");
        }

        return true;
    }

    public function generateManualReport()
    {
        $company_ids = ReportNotification::distinct("company_id")->pluck("company_id");

        foreach ($company_ids as $company_id) {
            $this->report($company_id, "Manual Entery", "daily_manual.pdf", "ME");
        }

        return true;
    }

    public function report($company_id, $report_type, $file_name, $status  = null)
    {

        $date = date("Y-m-d", strtotime("-2 days"));


        $info = (object)[
            // 'total_employee' => Employee::whereCompanyId($company_id)->count(),
            // 'total_present' => $this->getCountByStatus($company_id, "P", $date),
            // 'total_absent' => $this->getCountByStatus($company_id, "A", $date),
            // 'total_missing' => $this->getCountByStatus($company_id, "---", $date),

            'total_employee' => 0,
            'total_present' => 0,
            'total_absent' => 0,
            'total_missing' => 0,


            'total_early' => 0,
            'total_late' => 0,
            'total_leave' => 0,
            'department_name' => 'All',
            "daily_date" => $date,
            'report_type' => $report_type
        ];


        $data = $this->getModelByQuery($company_id, $date)->get();
        // return $model = $this->getModel($company_id, $date);

        // if ($status !== null) {
        //     $model->where('status', $status);
        // }

        $company = Company::whereId($company_id)->with('contact')->first(["logo", "name", "company_code", "location", "p_o_box_no", "id"]);

        $pdf = Pdf::loadView('pdf.daily', compact("company", "info", "data"))->output();

        Storage::disk('local')->put($company_id . '/' . $file_name, $pdf);

        return "Daily report generated.";
    }

    public function getModelByQuery($company_id, $date)
    {
        $date = date("Y-m-04");

        return DB::table("attendances as a")
            ->where('a.company_id', $company_id)
            ->whereDate('date', $date)
            ->join('employees as e', 'a.employee_id', '=', 'e.system_user_id')
            // ->leftJoin('devices as d_in', 'd_in.id', '=', 'a.device_id_in')
            // ->leftJoin('devices as d_out', 'd_out.device_id', '=', 'a.device_id_out')
            ->select(
                "a.date",
                "a.device_id_in",
                "a.shift_id",
                "a.status",
                "a.in",
                "a.out",
                "a.total_hrs",
                "a.ot",
                "a.late_coming",
                "a.early_going",
                "a.device_id_in",
                "a.device_id_out",
                "e.system_user_id",
                "e.display_name",
                "e.employee_id",
                "e.department_id",
                "e.profile_picture",
                // "d_in.id",
                // "d_in.short_name as device_in_short_name",
                // "d_out.short_name as device_out_short_name",
            );


        $model = Attendance::query();
        $model->where('company_id', 1);
        $model->whereDate('date', $date);


        $model->with([
            "schedule.shift:id,name,working_hours,overtime_interval,on_duty_time,off_duty_time,late_time,early_time,beginning_in,ending_in,beginning_out,ending_out,absent_min_in,absent_min_out,days",
            "schedule.shift_type:id,name",
        ]);

        return $model;
    }

    public function getModel($company_id, $date)
    {
        $model = Attendance::query();
        $model->where('company_id', $company_id);
        $model->whereDate('date', $date);


        $model->with([
            "employee:id,system_user_id,first_name,employee_id,department_id,profile_picture",
            "device_in:id,name,short_name,device_id,location",
            "device_out:id,name,short_name,device_id,location",
            "schedule.shift:id,name,working_hours,overtime_interval,on_duty_time,off_duty_time,late_time,early_time,beginning_in,ending_in,beginning_out,ending_out,absent_min_in,absent_min_out,days",
            "schedule.shift_type:id,name",
        ]);

        return $model;
    }

    public function getCountByStatus($company_id, $status, $date)
    {
        return DB::table("attendances")->where("company_id", $company_id)->whereDate('date', $date)->where('status', $status)->count();
    }

    public function getLogo($logo)
    {
        if (env('APP_ENV') !== 'local') {
            return $logo;
        } else {
            return getcwd() . '/upload/app-logo.jpeg';
        }
    }

    public function getHTML($data, $company, $info)
    {

        $str = '';

        $str .= '<!DOCTYPE html>';
        $str .= '<html>';
        $str .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $str .= '<body>';

        $str .= '<table style="margin-top: -20px !important;backgroundd-color:blue;padding-bottom:0px ">';
        $str .= '<tr>';
        $str .= '<td style="text-align: left;width: 300px; border :none; padding:15px;   backgrozund-color: red">';
        $str .= '<div><br><br><br>';
        $str .= '<img src="' . $this->getLogo($company->logo) . '" height="120px" width="180px">';
        $str .= '<table style="text-align: right; border :none; width:180px; margin-top:5px;backzground-color:blue">';
        $str .= '<tr style="text-align: left; border :none;">';
        $str .= '<td style="text-align: right; border :none;font-size:10px">';
        $str .= '<b>' . $company->name ?? '---' . '</b><br>';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '<tr style="text-align: left; border :none;">';
        $str .= '<td style="text-align: right; border :none;font-size:10px">';
        $str .= '<span style="margin-right: 3px">P.O.Box ' . $company->p_o_box_no ?? '---' . '</span><br>';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '<tr style="text-align: left; border :none;">';
        $str .= '<td style="text-align: right; border :none;font-size:10px">';
        $str .= '<span style="margin-right: 3px">' . $company->location ?? '---' . '</span><br>';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '<tr style="text-align: left; border :none;">';
        $str .= '<td style="text-align: right; border :none;font-size:10px">';
        $str .= '<span style="margin-right: 3px">' . $company->contact->number ?? '---' . '</span><br>';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '</table>';
        $str .= '</div>';
        $str .= '</td>';


        $str .= '<td style="text-align: left;width: 333px; border :none; padding:15px; backgrozusnd-color:blue">';
        $str .= '<div>';
        $str .= '<table style="text-align: left; border :none;">';
        $str .= '<tr style="text-align: left; border :none;">';
        $str .= '<td style="text-align: center; border :none">';
        $str .= '<span class="title-font">Daily Attendance ' . $info->report_type . ' Report</span>';
        $str .= '<hr style="width: 230px">';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '<tr style="text-align: left; border :none;">';
        $str .= '<td style="text-align: center; border :none">';
        $str .= ' <span style="font-size: 11px">' . date('d M Y', strtotime($info->daily_date)) . '<br>';
        $str .= '<small>Department : ' . $info->department_name . '</small>';
        $str .= '</span>';
        $str .= '<hr style="width: 230px">';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '</table>';
        $str .= '</div>';
        $str .= '</td>';

        $str .= '<td style="text-align: right;width: 300px; border :none; backgrounsd-color: red">';
        $str .= '<table class="summary-table" style="border:none; padding:0px 50px; margin-left:35px;margin-top:20px;margin-bottom:0px">';
        $str .= '<tr style="border: none">';
        $str .= '<th style="text-align: center; border :none;padding:10px;font-size: 12px " colspan="3">';
        $str .= '<hr style="width: 200px">';
        $str .= 'Total Number of Employees : ' . $info->total_employee;
        $str .= '</th>';
        $str .= '</tr>';

        $str .= '<tr class="summary-header" style="border: none;background-color:#eeeeee">';
        $str .= '<th style="text-align: center; border :none; padding:5px">Present</th>';
        $str .= '<th style="text-align: center; border :none">Absent</th>';
        $str .= '<th style="text-align: center; border :none">Leave</th>';
        $str .= '</tr>';

        $str .= '<tr style="border: none">';
        $str .= '<td style="text-align: center; border :none; padding:5px;color:green">' . $info->total_present ?? 0  . '</td>';
        $str .= '<td style="text-align: center; border :none; padding:5px;color:red">' . $info->total_absent ?? 0  . '</td>';
        $str .= '<td style="text-align: center; border :none; padding:5px;color:red">' . $info->total_leave ?? 0  . '</td>';
        $str .= '</tr>';

        $str .= '<tr class="summary-header" style="border: none;background-color:#eeeeee">';
        $str .= '<th style="text-align: center; border :none; padding:5px">Late</th>';
        $str .= '<th style="text-align: center; border :none">Early</th>';
        $str .= '<th style="text-align: center; border :none">Missing</th>';
        $str .= '</tr>';

        $str .= '<tr style="border: none">';
        $str .= '<td style="text-align: center; border :none; padding:5px;color:red">' . $info->total_late ?? 0  . '</td>';
        $str .= '<td style="text-align: center; border :none; padding:5px;color:green">' . $info->total_early ?? 0  . '</td>';
        $str .= '<td style="text-align: center; border :none; padding:5px;color:orange">' . $info->total_missing ?? 0  . '</td>';
        $str .= '</tr>';


        $str .= '<tr style="border: none">';
        $str .= '<th style="text-align: center; border :none" colspan="3">';
        $str .= '<hr style="width: 200px">';
        $str .= '</th>';
        $str .= '</tr>';
        $str .= '</table>';
        $str .= '</br>';
        $str .= '</td>';

        $str .= '<hr style="margin:0px;padding:0">';

        $str .= '<div id="footer">';
        $str .= '<div class="pageCounter">';
        $str .= '<p></p>';

        $str .= $this->pageCounter($data);

        $str .= '</div>';
        $str .= '<div id="pageNumbers">';
        $str .= '<div class="page-number" style="font-size: 9px"></div>';
        $str .= '</div>';
        $str .= '</div>';

        $str .= '<footer id="page-bottom-line" style="padding-top: 100px!important">';
        $str .= '<hr style="width: 100%;">';
        $str .= '<table class="footer-main-table">';
        $str .= '<tr style="border :none">';
        $str .= '<td style="text-align: left;border :none"><b>Device</b>: Main Entrance = MED, Back Entrance = BED</td>';
        $str .= '<td style="text-align: left;border :none"><b>Shift Type</b>: Manual = MA, Auto = AU, NO = NO</td>';
        $str .= '<td style="text-align: left;border :none"><b>Shift</b>: Morning = Mor, Evening = Eve, Evening2 = Eve2</td>';
        $str .= '<td style="text-align: right;border :none;">';

        $str .= '<b>Powered by</b>: <span style="color:blue">';
        $str .= '<a href="https://ideahrms.com/" target="_blank">ideahrms.com</a>';
        $str .= '</span>';
        $str .= '</td>';
        $str .= '<td style="text-align: right;border :none">Printed on :' . date('d-M-Y ') . '</td>';
        $str .= '</tr>';
        $str .= '</table>';
        $str .= '</footer>';
        $str .= $this->renderTable($data);
        $str .= '</body>';
        $str .= '</html>';


        $str .= $this->getStyles();

        return $str;
    }

    public function parseData($data)
    {
        $arr = [];

        foreach ($data as $value) {
            $arr[] = [
                "first_name" => $value->employee->first_name,
                "employee_id" => $value->employee_id,
                "in" => $value->in,
                "out" => $value->out,
                "total_hrs" => $value->total_hrs,
                "ot" => $value->ot,
                "status" => $value->status,
                "d_in" => "in",
                "d_out" => "out",
            ];
        }
        return $arr;
    }

    public function renderTable($data)
    {

        // $data = $this->parseData($data);
        $statusColor = '';
        $str = '';

        $i = 0;

        $str .= '<table class="main-table">';

        $str .= '<tr style="text-align: left;font-weight:bold">';
        $str .= '<td style="text-align:  left;"> # </td>';
        $str .= '<td style="text-align:  left;"> Name </td>';
        $str .= '<td style="text-align:  center;width:80px"> EID </td>';
        $str .= '<td style="text-align:  center;width:80px"> In </td>';
        $str .= '<td style="text-align:  center;width:80px"> Out </td>';
        $str .= '<td style="text-align:  center;width:80px"> Total Hours </td>';
        $str .= '<td style="text-align:  center;width:80px"> OT </td>';
        $str .= '<td style="text-align:  center;width:80px"> Status </td>';
        $str .= '<td style="text-align:  center;width:150px"> Device In </td>';
        $str .= '<td style="text-align:  center;width:150px"> Device Out </td>';
        $str .= '</tr>';
        foreach ($data as $data) {

            $str .= '<tbody>';

            $str .= '<tr style="text-align:  center;">';
            $str .= '<td>' . ++$i . '</td>';
            $str .= '<td style="text-align:  left; width:120px">' . $data->display_name . '</td>';
            $str .= '<td>' . $data->employee_id . '</td>';
            $str .= '<td> ' . $data->in . ' </td>';
            $str .= '<td> ' . $data->out . ' </td>';
            $str .= '<td> ' . $data->total_hrs . ' </td>';
            $str .= '<td> ' . $data->ot . ' </td>';
            $str .= '<td style="text-align:  center; color:' . $statusColor  . '"> ' . $data->status . ' </td>';
            $str .= '<td> ' . $data->device_in_short_name  . ' </td>';
            // $str .= '<td> ' . $data->device_out_short_name . ' </td>';

            $str .= '</tr>';
            $str .= '</tbody>';
        }
        $str .= '</table>';
        return $str;
    }

    public function pageCounter($data): string
    {
        $str = '';

        $p = count($data) / 50;

        if ($p <= 1) {
            $str .= '<span></span>';
        } else {
            for ($a = 1; $a <= $p; $a++) {
                $str .= '<span></span>';
            }
        }
        return $str;
    }

    public function getStyles(): string
    {
        $str = '';

        $str .= '<style>';

        $str .= '.pageCounter span{counter-increment:pageTotal}#pageNumbers div:before{counter-increment:currentPage;content:"Page "counter(currentPage) " of "}#pageNumbers div:after{content:counter(pageTotal)}#footer{position:fixed;top:720px;right:0;bottom:0;text-align:center;font-size:12px}#page-bottom-line{position:fixed;right:0;bottom:-6px;text-align:center;font-size:12px;counter-reset:pageTotal}#footer .page:before{content:counter(page,decimal)}#footer .page:after{counter-increment:counter(page,decimal)}@page{margin:20px 30px 40px 50px}table{font-family:arial,sans-serif;border-collapse:collapse;border:none;width:100%}td,th{border:1px solid #eee;text-align:left}tr:nth-child(even){border:1px solid #eee}th{font-size:9px}td{font-size:7px}footer{bottom:0;position:absolute;width:100%}.main-table{padding-bottom:20px;padding-top:10px;padding-right:15px;padding-left:15px}.footer-main-table{padding-bottom:7px;padding-top:0;padding-right:15px;padding-left:15px}hr{position:relative;border:none;height:2px;background:#c5c2c2;padding:0}
        .title-font{font-family:Arial,Helvetica,sans-serif !important;font-size:14px;font-weight:700}.summary-header th{font-size:10px}.summary-table td{font-size:9px}';

        $str .= '</style>';


        return $str;
    }
}
