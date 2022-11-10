<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return $this->report($request)->paginate($request->per_page);
    }

    public function report($request)
    {

        $model = Attendance::query();
        $model->where('company_id', $request->company_id);

        $model->when($request->filled('employee_id'), function ($q) use ($request) {
            $q->where('employee_id', $request->employee_id);
        });

        $model->when($request->department_id && $request->department_id != -1, function ($q) use ($request) {
            $ids = Employee::where("department_id", $request->department_id)->pluck("employee_id");
            $q->whereIn('employee_id', $ids);
        });

        $model->when($request->status == "P", function ($q) {
            $q->where('status', "P");
        });

        $model->when($request->status == "A", function ($q) {
            $q->where('status', "A");
        });

        $model->when($request->status == "---", function ($q) {
            $q->where('status', "---");
        });

        $model->when($request->late_early == "L", function ($q) {
            $q->where('late_coming', "!=", "---");
        });

        $model->when($request->late_early == "E", function ($q) {
            $q->where('early_going', "!=", "---");
        });

        $model->when($request->ot == 1, function ($q) {
            $q->where('ot', "!=", "---");
        });

        $model->when($request->daily_date && $request->report_type == 'Daily', function ($q) use ($request) {
            $q->whereDate('date', $request->daily_date);
            $q->orderBy("id", "desc");
        });

        $model->when($request->from_date && $request->to_date && $request->report_type != 'Daily', function ($q) use ($request) {
            $q->whereBetween("date", [$request->from_date, $request->to_date]);
            $q->orderBy("date", "asc");
        });

        $model->with([
            "employee:id,system_user_id,first_name,employee_id,department_id,profile_picture",
            "device_in:id,name,short_name,device_id,location",
            "device_out:id,name,short_name,device_id,location",
            "schedule.shift:id,name,working_hours,overtime_interval,on_duty_time,off_duty_time,late_time,early_time,beginning_in,ending_in,beginning_out,ending_out,absent_min_in,absent_min_out,days",
            "schedule.shift_type:id,name",
        ]);

        return $model;
    }
}
