<?php

namespace App\Http\Controllers\Reports;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class WeeklyController extends Controller
{
    public function weekly(Request $request)
    {
        return $this->processPDF($request)->stream();
    }
    public function weekly_download_pdf(Request $request)
    {
        return $this->processPDF($request)->download();
    }

    public function weekly_download_csv(Request $request)
    {
        $model = new ReportController;

        $data = $model->report($request)->get();

        $fileName = 'report.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            $i = 0;

            fputcsv($file, ["#", "Date", "E.ID", "Name", "Dept", "Shift Type", "Shift", "Status", "In", "Out", "Total Hrs", "OT", "Late coming", "Early Going", "D.In", "D.Out"]);
            foreach ($data as $col) {
                fputcsv($file, [
                    ++$i,
                    $col['date'],
                    $col['employee_id'] ?? "---",
                    $col['employee']["first_name"] ?? "---",
                    $col['employee']["department"]["name"] ?? "---",
                    $col['schedule']["shift_type"]["name"] ?? "---",
                    $col['schedule']["shift"]["name"] ?? "---",
                    $col["status"] ?? "---",
                    $col["in"] ?? "---",
                    $col["out"] ?? "---",
                    $col["total_hrs"] ?? "---",
                    $col["ot"] ?? "---",
                    $col["late_coming"] ?? "---",
                    $col["early_going"] ?? "---",
                    $col["device_in"]["short_name"] ?? "---",
                    $col["device_out"]["short_name"] ?? "---"
                ], ",");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function processPDF($request)
    {
        $start = $request->from_date ?? date('Y-10-01');
        $end = $request->to_date ?? date('Y-10-07');

        $model = Attendance::query();
        $model = $model->whereBetween('date', [$start, $end]);
        $model->orderBy('date', 'asc');

        $model->when($request->department_id && $request->department_id != -1, function ($q) use ($request) {
            $ids = Employee::where("department_id", $request->department_id)->pluck("employee_id");
            $q->whereIn('employee_id', $ids);
        });

        $data = $model->get()->groupBy(['employee_id', 'date']);

        $pdf = App::make('dompdf.wrapper');

        $company = Company::whereId($request->company_id)->with('contact:id,company_id,number')->first(["logo", "name", "company_code", "location", "p_o_box_no", "id"]);
        $company['department_name'] = DB::table('departments')->whereId($request->department_id)->first(["name"])->name ?? '';
        $company['report_type'] = $this->getStatusText($request->status);
        $company['start'] = $start;
        $company['end'] = $end;

        return $pdf->loadHTML($this->getHTML($data, (object)$company));
    }

    public function getHTML($data, $company)
    {

        $mob = $company->contact->number ?? '---';
        $companyLogo = "";

        if (env('APP_ENV') !== 'local') {
            $companyLogo = $company->logo;
        } else {
            $companyLogo = getcwd() . "/upload/app-logo.jpeg";
        }

        if ($company->p_o_box_no == "null") {
            $company->p_o_box_no = "---";
        }

        //  <img src="' . getcwd() . '/upload/app-logo.jpeg" height="70px" width="200">
        // <img src="' . $companyLogo . '" height="100px" width="100">      <img src="' . $companyLogo . '" height="100px" width="100">

        return '
        <!DOCTYPE html>
            <html>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <head>
            <style>
                table { font-family: arial, sans-serif; border-collapse: collapse; border: none; width: 100%; }
                td, th { border: 1px solid #eeeeee; text-align: left; }

                th { font-size: 9px; }
                td { font-size: 7px; }

                .page-break { page-break-after: always; }
                .main-table {
                    padding-right: 15px;
                    padding-left: 15px;
                }
                hr {
                    position: relative;
                    border: none;
                    height: 2px;
                    background: #c5c2c2;
                    padding: 0px
                }
                .title-font {
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 14px;
                    font-weight: bold
                }

                .summary-header th {
                    font-size: 10px
                }

                .summary-table td {
                    font-size: 9px
                }

                footer {
                    bottom: 0px;
                    position: absolute;
                    width: 100%;
                }

                #footer {
                    position: fixed;
                    top: 720px;
                    right: 0px;
                    bottom: 0px;
                    text-align: center;
                    font-size: 12px;
                }

                #page-bottom-line {
                    position: fixed;
                    right: 0px;
                    bottom: -14px;
                    text-align: center;
                    font-size: 12px;
                    counter-reset: pageTotal;

                }

                .pageCounter span {
                    counter-increment: pageTotal;
                }

                #pageNumbers div:before {
                    counter-increment: currentPage;
                    content: "Page "counter(currentPage) " of ";
                }

                #pageNumbers div:after {
                    content: counter(pageTotal);
                }
                @page {
                    margin: 20px 30px 40px 50px;
                }

                .footer-main-table {
                    padding-bottom: -100px;
                    padding-top: -50px;
                    padding-right: 15px;
                    padding-left: 15px;
                }

                .main-table {
                    padding-bottom: 0px;
                    padding-top: 0px;
                    padding-right: 15px;
                    padding-left: 15px;
                }

            </style>
            </head>
            <body>
            <table style="margin-top: -20px !important;backgroundd-color:blue;padding-bottom:0px ">
            <tr>
                <td style="text-align: left;width: 300px; border :none; padding:15px;   backgrozund-color: red">
                    <div style="img">
                    <img src="' . $companyLogo . '" height="100px" width="100">
                    </div>
                </td>
                <td style="text-align: left;width: 333px; border :none; padding:15px; backgrozusnd-color:blue">
                    <div>
                        <table style="text-align: left; border :none;  ">
                            <tr style="text-align: left; border :none;">
                                <td style="text-align: center; border :none">
                                    <span class="title-font">
                                    Weekly Attendance ' . $company->report_type . ' Report
                                    </span>
                                    <hr style="width: 230px">
                                </td>
                            </tr>
                            <tr style="text-align: left; border :none;">
                                <td style="text-align: center; border :none">
                                    <span style="font-size: 11px">
                                    ' . date('d M Y', strtotime($company->start))  . ' - ' .  date('d M Y', strtotime($company->end))  . ' <br>
                                       <small> Department : ' . $company->department_name . '</small>
                                    </span>
                                    <hr style="width: 230px">
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="text-align: right;width: 300px; border :none; backgrounsd-color: red">


                    <table class="summary-table"
                    style="border:none; padding:0px 50px; margin-left:35px;margin-top:20px;margin-bottom:0px">
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <b>
                            ' . $company->name . '
                            </b>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 3px"> P.O.Box: ' .  ($company->p_o_box_no ?? '---') . ' </span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 3px">' . $company->location . '</span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 3px"> Tel: ' .  $mob . ' </span>
                            <br>
                        </td>
                    </tr>
                </table>

                    <br>
                </td>
                </td>
            </tr>
        </table>
        <hr style="margin:0px;padding:0">
        <div id="footer">
        <div class="pageCounter">
            <p></p>
            ' . $this->getPageNumbers($data)  . '
        </div>
        <div id="pageNumbers" style="font-size: 9px;margin-top:5px">
            <div class="page-number"></div>
        </div>
        </div>
        <footer id="page-bottom-line" style="margin-top: 10000px!important;">
        <hr style="width: 100%;margin-top: 10px!important">
        <table class="footer-main-table" >
            <tr style="border :none;">
                <td style="text-align: left;border :none;font-size:9px"><b>Device</b>: Main Entrance = MED, Back Entrance = BED</td>
                <td style="text-align: left;border :none;font-size:9px"><b>Shift Type</b>: Manual = MA, Auto = AU, NO = NO</td>
                <td style="text-align: left;border :none;font-size:9px"><b>Shift</b>: Morning = Mor, Evening = Eve, Evening2 = Eve2
                </td>
                <td style="text-align: right;border :none;font-size:9px">
                    <b>Powered by</b>: <span style="color:blue"> www.ideahrms.com</span>
                </td>
                <td style="text-align: right;border :none;font-size:9px">
                    Printed on :  ' . date("d-M-Y ") . '
                </td>
            </tr>
        </table>
    </footer>
                ' . $this->renderTable($data) . '
    </body>
        </html>';
    }



    public function renderTable($data)
    {
        $str = "";
        foreach ($data as $eid => $row) {

            $emp = Employee::where("employee_id", $eid)->select("employee_id", "display_name", "system_user_id")->first();



            $str .= '<div class="page-breaks">';

            $str .= '<table class="main-table" style="margin-top: 10px !important;">';
            $str .= '<tr style="text-align: left; border :1px solid black; width:120px;">';
            $str .= '<td style="text-align:left;width:120px"><b>Name</b>:' . ($emp->display_name ?? ' ---') . '</td>';
            $str .= '<td style="text-align:left;width:120px"><b>EID</b>:' . $emp &&  $emp->employee_id ?? '' . '</td>';
            $str .= '<td style="text-align:left;width:120px"><b>Total Hrs</b>:' . $this->getCalculation($row)['work'] . '</td>';
            $str .= '<td style="text-align:left;width:120px"><b>OT</b>:' . $this->getCalculation($row)['ot'] . '</td>';
            $str .= '<td style="text-align:left;color:green;width:150px"><b>Present</b>:' . ($this->getCalculation($row)['presents']) . '</td>';
            $str .= '<td style="text-align:left;color:red;width:150px"><b>Absent</b>:' . ($this->getCalculation($row)['absents']) . '</td>';
            $str .= '<td style="text-align:left;color:orange"><b>Missing</b>:' . ($this->getCalculation($row)['missings']) . '</td>';
            $str .= '<td style="text-align:left;width:120px;"><b>Manual</b>:' . ($this->getCalculation($row)['manuals']) . '</td>';
            $str .= '</tr>';
            $str .= '</table>';

            $str .= '<table class="main-table" style="margin-top: 5px !important;  padding-bottom: 1px; ">';

            $dates = '<tr"><td style="text-align:left;width:100px"><b>Dates</b></td>';
            $days = '<tr"><td><b>Days</b></td>';
            $in = '<tr"><td><b>In</b></td>';
            $out = '<tr"><td><b>Out</b></td>';
            $work = '<tr"><td><b>Work</b></td>';
            $ot = '<tr"><td><b>OT</b></td>';
            $shift = '<tr"><td><b>Shift</b></td>';
            $shift_type = '<tr "><td><b>Shift Type</b></td>';
            $din = '<tr"><td><b>Device In</b></td>';
            $dout = '<tr"><td><b>Device Out</b></td>';
            $status_tr = '<tr"><td><b>Status</b></td>';


            foreach ($row as $key => $record) {

                $dates .= '<td style="text-align: center;"> ' . substr($key, 0, 2) . ' </td>';
                $days .= '<td style="text-align: center;"> ' . $record[0]['day'] . ' </td>';

                $in .= '<td style="text-align: center;"> ' . $record[0]['in'] . ' </td>';
                $out .= '<td style="text-align: center;"> ' . $record[0]['out'] . ' </td>';

                $work .= '<td style="text-align: center;"> ' . $record[0]['total_hrs']  . ' </td>';
                $ot .= '<td style="text-align: center;"> ' . $record[0]['ot'] . ' </td>';

                $shift .= '<td style="text-align: center;"> ' . $record[0]['shift_id'] . ' </td>';
                $shift_type .= '<td style="text-align: center;"> ' . $record[0]['shift_type_id'] . ' </td>';
                $din .= '<td style="text-align: center;"> ' . $record[0]['device_id_in'] . ' </td>';
                $dout .= '<td style="text-align: center;"> ' . $record[0]['device_id_out'] . ' </td>';

                $status = $record[0]['status'] == 'A' ? 'red' : 'green';

                $status_tr .= '<td style="text-align: center; color:' . $status . '"> ' . $record[0]['status'] . ' </td>';
            }


            $dates .= '</tr>';
            $days .= '</tr>';
            $in .= '</tr>';
            $out .= '</tr>';
            $work .= '</tr>';
            $ot .= '</tr>';
            $shift .= '</tr>';
            $shift_type .= '</tr>';
            $din .= '</tr>';
            $dout .= '</tr>';
            $status_tr .= '</tr>';

            $str = $str . $dates . $days . $in . $out . $work . $ot . $shift . $shift_type . $din . $dout . $status_tr;

            $str .= '</table>';
            $str .= '</div>';
        }
        return $str;
    }

    public function getCalculation($arr)
    {
        $work_minutes = 0;
        $ot_minutes = 0;

        $presents = 0;
        $absents = 0;
        $missings = 0;
        $manuals = 0;

        foreach ($arr as $a) {
            $status = $a[0]->status;
            $work   = $a[0]->total_hrs;
            $ot     = $a[0]->ot;

            if ($status == 'P') {
                $presents++;
            } else if ($status == 'A') {
                $absents++;
            } else if ($status == 'ME') {
                $missings++;
            } else if ($status == '---') {
                $manuals++;
            }

            if ($work != '---') {
                list($work_hour, $work_minute) = explode(':', $work);
                $work_minutes += $work_hour * 60;
                $work_minutes += $work_minute;
            }

            if ($ot != '---' && $ot != 'NA') {
                list($ot_hour, $ot_minute) = explode(':', $ot);
                $ot_minutes += $ot_hour * 60;
                $ot_minutes += $ot_minute;
            }
        }

        $work_hours = floor($work_minutes / 60);
        $work_minutes -= $work_hours * 60;

        $ot_hours = floor($ot_minutes / 60);
        $ot_minutes -= $ot_hours * 60;

        return [
            'work' => $work_hours . ':' . $work_minutes,
            'ot' => $ot_hours . ':' . $ot_minutes,
            'presents' => $presents,
            'absents'  => $absents,
            'missings' => $missings,
            'manuals'  => $manuals
        ];
    }


    public function getPageNumbers($data)
    {
        $p = count($data);
        $str = '';
        $l = $p / 4;
        if ($p <= 3) {
            $str .= '<span></span>';
        } else if ($p <= 5) {
            $str .= '<span></span><span></span>';
        } else {
            for ($a = 1; $a <= $l; $a++) {
                $str .= '<span></span>';
            }
        }
        return $str;
    }
    public function weekly_html(Request $request)
    {
        return Pdf::loadView('pdf.html.weekly.weekly_summary')->stream();
    }
}