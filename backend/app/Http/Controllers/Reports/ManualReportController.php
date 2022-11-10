<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use App\Models\ScheduleEmployee;
use Illuminate\Http\Request;

class ManualReportController extends Controller
{
    public function custom_report(Request $request)
    {
        $model = Attendance::query();

        $model->whereHas("shift_type", function ($q) {
            $q->where('slug', "manual_shift");
        });

        $model->when($request->employee_id, function ($q) use ($request) {
            $q->where('employee_id', $request->employee_id);
        });

        $model->when($request->department_id && $request->department_id != -1, function ($q) use ($request) {
            $ids = Employee::where("department_id", $request->department_id)->pluck("system_user_id");
            $q->whereIn('employee_id', $ids);
        });

        $model->when($request->from_date && $request->to_date, function ($q) use ($request) {
            $q->whereBetween("date", [$request->from_date, $request->to_date]);
        });

        $model->when($request->status == "P", function ($q){
            $q->where('status', "P");
        });

        $model->when($request->status == "A", function ($q){
            $q->where('status', "A");
        });

        $model->when($request->status == "M", function ($q){
            $q->where('status', "---");
        });

        $model->when($request->late_early == "L", function ($q){
            $q->where('late_coming', "!=", "---");
        });

        $model->when($request->late_early == "E", function ($q){
            $q->where('early_going', "!=", "---");
        });

        $model->when($request->ot == 1, function ($q) {
            $q->where('ot', "!=", "---");
        });

        $model->with(["employee", "shift", "shift_type", "time_table", "device_out", "device_in"]);

        return $model->orderByDesc("date")->paginate($request->per_page);
    }

    public function store()
    {
        $model = ScheduleEmployee::query();

        $model = $model->whereHas("shift_type", function ($q) {
            $q->where('slug', "manual_shift");
        });

        $rows = $model->get();

        $arr = [];

        foreach ($rows as $row) {
            $exist = Attendance::whereDate("date", date("Y-m-d"))->where("employee_id", $row->employee_id)->first();
            $item = [
                "company_id" =>  $row->company_id,
                "employee_id" =>  $row->employee_id,
                "shift_type_id" => 3
            ];

            if (count($row->logs) > 0) {
                $item = $item + $this->process_columns($row);
            }

            if ($exist) {
                Attendance::whereDate("date", date("Y-m-d"))->where("employee_id", $row->employee_id)->update($item);
            } else {
                $item["date"] = date("Y-m-d");
                Attendance::create($item);
            }

            $arr[] = $item;
        }
        return Attendance::count();
    }

    public function getStatus($row)
    {

        $time_table = $row->shift->time_table;
        $on_duty_time = $time_table->on_duty_time;
        $absent_min_in = $time_table->absent_min_in;
        $off_duty_time = $time_table->off_duty_time;
        $absent_min_out = $time_table->absent_min_out;
        $beginning_out = $time_table->beginning_out;

        $first = $row->first_log->time;
        $last = $row->last_log->time;

        $cap = strtotime($beginning_out); // 1500

        $in = strtotime($first);
        $out = strtotime($last);

        if (($in > $cap || $out < $cap) || ($first == $last)) {
            return "---";
        }

        $absent_in = strtotime("$on_duty_time + $absent_min_in minute");
        $absent_out = strtotime("$off_duty_time - $absent_min_out minute");

        return $in > $absent_in || $out < $absent_out ? "A" : "P";
    }

    public function getCheckInDate($row)
    {
        return $row->first_log->date ?? "---";
    }

    public function getCheckOutDate($row)
    {
        return $row->first_log->date != $row->last_log->date ? $row->last_log->date : "---";
    }

    public function getCheckIn($row)
    {
        $log = $row->first_log;
        $shift = $row->shift;
        return (strtotime($log->time) <= strtotime($shift->time_table->beginning_out)) ? $log->time : "---";
    }

    public function getCheckOut($row)
    {
        $log = $row->last_log;
        $shift = $row->shift;
        return (strtotime($log->time) > strtotime($shift->time_table->beginning_out)) ? $log->time : "---";
    }

    public function getTotalHrsMins($row)
    {
        $diff = abs(($row->last_log->show_log_time - $row->first_log->show_log_time));

        $h = floor($diff / 3600);
        $m = floor($diff % 3600) / 60;
        return (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
    }

    public function getOT($row)
    {
        if (!$row->isOverTime) {
            return "---";
        }

        $diff = abs(($row->last_log->show_log_time - $row->first_log->show_log_time));

        $diff = $diff - $row->shift->working_hours * 3600;

        $h = floor($diff / 3600);
        $h = $h < 0 ? "0" : $h;
        $m = floor($diff % 3600) / 60;
        $m = $m < 0 ? "0" : $m;

        return (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
    }

    public function getLateComing($row)
    {
        $time_table = $row->shift->time_table;
        $duty_time = $time_table->on_duty_time;

        $late_condition = strtotime("$duty_time + $time_table->late_time minute");

        $in = strtotime($row->first_log->time);

        if ($in <= $late_condition) {
            return "---";
        } else if ($in > strtotime($time_table->beginning_out)) {
            return "---";
        }

        $diff = abs((strtotime($duty_time) - $in));

        $h = floor($diff / 3600);
        $m = floor($diff % 3600) / 60;
        return (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
    }

    public function getEarlyGoing($row)
    {
        $time_table = $row->shift->time_table;
        $duty_time = $time_table->off_duty_time;

        $out = strtotime($row->last_log->time);
        $diff = abs((strtotime($duty_time) - $out));

        if ($out <= strtotime($time_table->beginning_out)) {
            return "---";
        } else if ($out >= strtotime($duty_time)) {
            return "---";
        }

        $h = floor($diff / 3600);
        $h = $h < 0 ? "0" : $h;
        $m = floor($diff % 3600) / 60;
        $m = $m < 0 ? "0" : $m;

        return (($h < 10 ? "0" . $h : $h) . ":" . ($m < 10 ? "0" . $m : $m));
    }

    public function getDeviceIn($row)
    {
        $time_table = $row->shift->time_table;
        $cap = strtotime($time_table->beginning_out); // 1500
        $in = strtotime($row->first_log->time); // 1500

        if (($in > $cap)) {
            return "---";
        }

        return Device::where("device_id", $row->first_log->DeviceID)->pluck("id")[0] ?? "---";
    }

    public function getDeviceOut($row)
    {
        $time_table = $row->shift->time_table;
        $cap = strtotime($time_table->beginning_out); // 1500
        $in = strtotime($row->last_log->time); // 1500

        if (($in <= $cap)) {
            return "---";
        }

        return Device::where("device_id", $row->last_log->DeviceID)->pluck("id")[0] ?? "---";
    }

    public function process_columns($row)
    {
        $item = [];
        $item["status"] = $this->getStatus($row);
        $item["device_id_in"] = $this->getDeviceIn($row);
        $item["device_id_out"] = $this->getDeviceOut($row);
        $item["date_in"] = $this->getCheckInDate($row);
        $item["date_out"] = $this->getCheckOutDate($row);
        $item["in"] = $this->getCheckIn($row);
        $item["out"] = $this->getCheckOut($row);
        $item["total_hrs"] = $this->getTotalHrsMins($row);
        $item["ot"] = $this->getOT($row);
        $item["late_coming"] = $this->getLateComing($row);
        $item["early_going"] = $this->getEarlyGoing($row);
        $item["shift_id"] = $row->shift_id ?? 0;
        $item["time_table_id"] = $row->shift->time_table_id ?? 0;
        return $item;
    }
}
