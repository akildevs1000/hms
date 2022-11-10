<?php

namespace App\Http\Controllers;

use App\Http\Requests\Announcement\StoreRequest;
use App\Http\Requests\Announcement\UpdateRequest;
use App\Models\Announcement;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index(Announcement $model, Request $request)
    {
        return $model->with('departments')->where('company_id', $request->company_id)->paginate($request->per_page ?? 10);
    }
    public function store(StoreRequest $request)
    {
        try {
            $record = Announcement::create($request->except('departments', 'employee'));
            if ($record) {
                if (count($request->employee) > 0) {
                    if ($request->employee[0] == '---') {
                        $ids = Employee::get('id');
                        $record->employees()->attach($ids);
                        return $this->response('Announcement Successfully created.', $record, true);
                    }
                    $employees = Employee::whereIn('id', $request->employee)->get('id');
                    $record->employees()->attach($employees);
                    return $this->response('Announcement Successfully created.', $record, true);
                }

                if (count($request->departments) > 0) {
                    $record->departments()->attach($request->departments);
                    $employees = Employee::whereIn('department_id', $request->departments)->get('id');
                    $record->employees()->attach($employees);
                }
                return $this->response('Announcement Successfully created.', $record, true);
            } else {
                return $this->response('Announcement cannot create.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
    public function update(UpdateRequest $request, Announcement $Announcement)
    {
        try {
            $record = $Announcement->update($request->except('departments'));
            if ($record) {
                if (count($request->departments) > 0) {
                    $Announcement->departments()->sync($request->departments);
                }
                return $this->response('Announcement successfully updated.', $record, true);
            } else {
                return $this->response('Announcement cannot update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
    public function destroy(Announcement $Announcement, Request $request)
    {
        $record = $Announcement->delete();
        if ($record) {
            $Announcement->departments()->detach($request->departments);
            return $this->response('Announcement successfully deleted.', $record, true);
        } else {
            return $this->response('Announcement cannot delete.', null, false);
        }
    }
    public function search(Announcement $model, Request $request, $key)
    {
        return $model->where('title', 'LIKE', "%$key%")->with('departments')->paginate($request->per_page);
    }
    public function deleteSelected(Request $request)
    {
        $record = Announcement::whereIn('id', $request->ids)->delete();
        if ($record) {
            $record->departments()->detach($request->departments);
            DB::table('announcement_employee')->whereIn('announcement_id', $request->ids)->delete();
            return $this->response('Announcement Successfully delete.', $record, true);
        } else {
            return $this->response('Announcement cannot delete.', null, false);
        }
    }

    public function getAnnouncement($id)
    {
        $employee = Employee::where('id', $id)->first();
        $start = date("Y-m-d", strtotime(now()));
        $endOfMonthDate = date("Y-m-t", strtotime($start));
        return $employee->announcement()->whereBetween('start_date', [$start, $endOfMonthDate])->get();

    }
}
