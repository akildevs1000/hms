<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\ScheduleEmployee;
use App\Http\Requests\ScheduleEmployee\StoreRequest;
use App\Http\Requests\ScheduleEmployee\UpdateRequest;

class ScheduleEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ScheduleEmployee $model)
    {
        return $model->with("shift_type", "shift", "employee")->paginate($request->per_page);
    }
    // public function employees_by_departments_old(Employee $employee, Request $request, $id)
    // {
    //     return $employee->whereHas('schedule')
    //         ->withOut(["user", "department", "sub_department", "sub_department", "designation", "role", "schedule"])
    //         ->when($id != -1, function ($q) use ($id) {
    //             $q->where("department_id", $id);
    //         })
    //         ->get(["first_name", "system_user_id", "employee_id"]);
    // }

    public function employees_by_departments($id)
    {
        return  Employee::select("first_name", "system_user_id", "employee_id", "department_id")
            ->withOut(["user", "sub_department", "sub_department", "designation", "role", "schedule"])
            ->when($id != -1, function ($q) use ($id) {
                $q->where("department_id", $id);
            })
            ->get();
    }

    public function logs(Request $request, ScheduleEmployee $model)
    {
        $emps = $model->with("logs")->get();
        foreach ($emps as $emp) {
            $emp->new_logs = [$emp->logs->groupBy("date")];
        }
        return $emps;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, ScheduleEmployee $model)
    {
        $data = $request->validated();

        $arr = [];

        foreach ($data["employee_ids"] as $item) {
            $arr[] = [
                "shift_id" => $data["shift_id"] ?? 0,
                "isOverTime" => $data["isOverTime"],
                "employee_id" => $item,
                "shift_type_id" => $data["shift_type_id"],
                "company_id" => $data["company_id"],
            ];
        }

        try {
            $record = $model->insert($arr);

            if ($record) {
                return $this->response('Schedule Employee successfully added.', $record, true);
            } else {
                return $this->response('Schedule Employee cannot add.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ScheduleEmployee  $ScheduleEmployee
     * @return \Illuminate\Http\Response
     */
    public function show(ScheduleEmployee $ScheduleEmployee)
    {
        return $ScheduleEmployee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ScheduleEmployee  $ScheduleEmployee
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            $record = ScheduleEmployee::where('employee_id', $id)->update($request->validated());
            if ($record) {
                return response()->json(['status' => true, 'message' => 'Schedule Employee successfully updated']);
            } else {
                return response()->json(['status' => false, 'message' => 'Schedule Employee cannot update']);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ScheduleEmployee  $ScheduleEmployee
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $record = ScheduleEmployee::where("employee_id", $id)->delete();

        try {
            if ($record) {
                return $this->response('Employee Schedule deleted.', null, true);
            } else {
                return $this->response('Employee Schedule cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteSelected(Request $request)
    {
        $record = ScheduleEmployee::whereIn('id', $request->ids)->delete();
        if ($record) {
            return response()->json(['status' => true, 'message' => 'ScheduleEmployee Successfully Deleted']);
        } else {
            return response()->json(['status' => false, 'message' => 'ScheduleEmployee cannot Deleted']);
        }
    }
}