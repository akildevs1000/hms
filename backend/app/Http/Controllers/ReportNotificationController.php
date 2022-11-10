<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportNotification\StoreRequest;
use App\Http\Requests\ReportNotification\UpdateRequest;
use App\Models\ReportNotification;
use Illuminate\Http\Request;

class ReportNotificationController extends Controller
{
    public function index(ReportNotification $model, Request $request)
    {
        return $model->where('company_id', $request->company_id)->paginate($request->per_page);
    }

    public function store(StoreRequest $request)
    {
        try {
            $record = ReportNotification::create($request->validated());

            if ($record) {
                return $this->response('Report Notification created.', $record, true);
            } else {
                return $this->response('Report Notification cannot create.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function show(ReportNotification $ReportNotification)
    {
        return $ReportNotification;
    }

    public function update(UpdateRequest $request, ReportNotification $ReportNotification)
    {
        try {
            $record = $ReportNotification->update($request->validated());

            if ($record) {
                return $this->response('Report Notification updated.', $record, true);
            } else {
                return $this->response('Report Notification update.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
    
    public function destroy(ReportNotification $ReportNotification)
    {
        $record = $ReportNotification->delete();

        if ($record) {
            return $this->response('Report Notification deleted.', $record, true);
        } else {
            return $this->response('Report Notification cannot delete.', null, false);
        }
    }
}
