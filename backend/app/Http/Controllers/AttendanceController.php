<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function SyncAttendance()
    {
        $items = [];
        $model = AttendanceLog::query();
        $model->where("checked", false);
        $model->take(1000);
        if ($model->count() == 0) {
            return false;
        }
        $logs = $model->get(["id", "UserID", "LogTime", "DeviceID", "company_id"]);

        $i = 0;

        foreach ($logs as $log) {

            $date = date("Y-m-d", strtotime($log->LogTime));

            $AttendanceLog = new AttendanceLog;

            $orderByAsc = $AttendanceLog->where("UserID", $log->UserID)->whereDate("LogTime", $date);
            $orderByDesc = $AttendanceLog->where("UserID", $log->UserID)->whereDate("LogTime", $date);

            $first_log = $orderByAsc->orderBy("LogTime")->first() ?? false;
            $last_log =  $orderByDesc->orderByDesc('LogTime')->first() ?? false;

            $logs = $AttendanceLog->where("UserID", $log->UserID)->whereDate("LogTime", $date)->count();

            $item = [];
            $item["company_id"] = $log->company_id;
            $item["employee_id"] = $log->UserID;
            $item["date"] = $date;

            if ($first_log) {
                $item["in"] = $first_log->time;
                $item["status"] = "---";
                $item["device_id_in"] = Device::where("device_id", $first_log->DeviceID)->pluck("id")[0] ?? "---";
            }
            if ($logs > 1 && $last_log) {
                $item["out"] = $last_log->time;
                $item["device_id_out"] = Device::where("device_id", $last_log->DeviceID)->pluck("id")[0] ?? "---";
                $item["status"] = "P";
                $diff = abs(($last_log->show_log_time - $first_log->show_log_time));
                $h = floor($diff / 3600);
                $m = floor(($diff % 3600) / 60);
                $item["total_hrs"] = (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
            }


            $attendance = Attendance::whereDate("date", $date)->where("employee_id", $log->UserID);

            $attendance->first() ? $attendance->update($item) : Attendance::create($item);

            AttendanceLog::where("id", $log->id)->update(["checked" => true]);

            $i++;

            // $items[$date][$log->UserID] = $item;
        }

        return $i;
    }
    public function SyncAbsent($no_of_day = 1)
    {
        $day = date('Y-m-d', strtotime('-' . $no_of_day . ' days'));

        $employees = Employee::whereDoesntHave('attendances', function ($q) use ($day) {
            $q->whereDate('date', $day);
        })
            ->get(["employee_id", "company_id"]);

        if(count($employees) == 0) {
            return false;
        }

        $record = [];

        foreach ($employees as $employee) {
            $record[] = [
                "employee_id"   => $employee->employee_id,
                "date"          => $day,
                "status"        => "A",
                "company_id"    => $employee->company_id
            ];
        }

        Attendance::insert($record);

        return count($record);
    }

    public function SyncAbsentForMultipleDays()
    {
        $first = AttendanceLog::orderBy("id")->first();
        $today = date('Y-m-d');
        $startDate = $first->edit_date;
        $difference = strtotime($startDate) - strtotime($today);
        $days = abs($difference / (60 * 60) / 24);
        $arr = [];

        for ($i = $days; $i > 0; $i--) {
            $arr[] = $this->SyncAbsent($i);
        }

        return json_encode($arr);
    }

    public function ResetAttendance(Request $request)
    {
        $items = [];
        $model = AttendanceLog::query();
        $model->whereBetween("LogTime", [$request->from_date ?? date("Y-m-d"), $request->to_date ?? date("Y-m-d")]);
        $model->where("DeviceID",$request->DeviceID);

        if ($model->count() == 0) {
            return false;
        }
        $logs = $model->get(["id", "UserID", "LogTime", "DeviceID", "company_id"]);


        $i = 0;

        foreach ($logs as $log) {

            $date = date("Y-m-d", strtotime($log->LogTime));

            $AttendanceLog = new AttendanceLog;

            $orderByAsc = $AttendanceLog->where("UserID", $log->UserID)->whereDate("LogTime", $date);
            $orderByDesc = $AttendanceLog->where("UserID", $log->UserID)->whereDate("LogTime", $date);

            $first_log = $orderByAsc->orderBy("LogTime")->first() ?? false;
            $last_log =  $orderByDesc->orderByDesc('LogTime')->first() ?? false;

            $logs = $AttendanceLog->where("UserID", $log->UserID)->whereDate("LogTime", $date)->count();

            $item = [];
            $item["company_id"] = $log->company_id;
            $item["employee_id"] = $log->UserID;
            $item["date"] = $date;

            if ($first_log) {
                $item["in"] = $first_log->time;
                $item["status"] = "---";
                $item["device_id_in"] = Device::where("device_id", $first_log->DeviceID)->pluck("id")[0] ?? "---";
            }
            if ($logs > 1 && $last_log) {
                $item["out"] = $last_log->time;
                $item["device_id_out"] = Device::where("device_id", $last_log->DeviceID)->pluck("id")[0] ?? "---";
                $item["status"] = "P";
                $diff = abs(($last_log->show_log_time - $first_log->show_log_time));
                $h = floor($diff / 3600);
                $m = floor(($diff % 3600) / 60);
                $item["total_hrs"] = (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
            }


            $attendance = Attendance::whereDate("date", $date)->where("employee_id", $log->UserID);

            $attendance->first() ? $attendance->update($item) : Attendance::create($item);

            AttendanceLog::where("id", $log->id)->update(["checked" => true]);

            $i++;

            $items[$date][$log->UserID] = $item;
        }

        Storage::disk('local')->put($request->DeviceID . '-' . date("d-M-y") . '-reset_attendance.txt', json_encode($items));

        return $i;
    }
}
