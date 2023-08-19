<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationReportTypes\StoreNotificationReportTypesRequest;
use App\Http\Requests\NotificationReportTypes\UpdateNotificationReportTypesRequest;
use App\Models\NotificationReportTypes;
use Illuminate\Http\Request;

class NotificationReportTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = NotificationReportTypes::query();

        //datatable Filters
        if ($request->filled('description')) {
            $model->where('description', 'ILIKE', "$request->description%");}

        if ($request->filled('name')) {
            $model->where('name', 'ILIKE', "$request->name%");}

        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';
            $model->orderBy($request->sortBy, $sortDesc);

        } else {
            $model->orderBy('name', 'ASC');
        }

        return $model->paginate($request->per_page);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNotificationReportTypesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNotificationReportTypesRequest $request)
    {

        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsEmail = NotificationReportTypes::where('company_id', $request->company_id)->where('name', $request->email)->count();
                if ($verifyIsEmail == 0) {

                    $record = NotificationReportTypes::create($data);

                    if ($record) {
                        return $this->response('Notification details are successfully created', $record, true);
                    } else {
                        return $this->response('Notification details are not created', $record, false);
                    }

                } else {
                    return $this->response($request->email . ' : Report Name is already exist. ', $data, false);
                }

            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function storeSingle(StoreNotificationReportTypesRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsEmail = NotificationReportTypes::where('company_id', $request->company_id)->where('name', $request->email)->count();
                if ($verifyIsEmail == 0) {

                    $record = NotificationReportTypes::create($data);

                    if ($record) {
                        return $this->response('Notification details are successfully created', $record, true);
                    } else {
                        return $this->response('Notification details are not created', $record, false);
                    }

                } else {
                    return $this->response($request->email . ' : Report Name is already exist. ', $data, false);
                }

            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NotificationReportTypes  $NotificationReportTypes
     * @return \Illuminate\Http\Response
     */
    public function show(NotificationReportTypes $NotificationReportTypes, $id)
    {
        return NotificationReportTypes::where('id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NotificationReportTypes  $NotificationReportTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(NotificationReportTypes $NotificationReportTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNotificationReportTypesRequest  $request
     * @param  \App\Models\NotificationReportTypes  $NotificationReportTypes
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNotificationReportTypesRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isRoomExist = NotificationReportTypes::where('name', $request->email)
                    ->where('company_id', $request->company_id)
                    ->first();

                if ($isRoomExist) {
                    if ($isRoomExist->id != $id) {
                        return $this->response($request->email . 'Name is already Exist', null, false);
                    }
                }
                $status = NotificationReportTypes::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Report details are updated succesfully', $status, true);
                } else {
                    return $this->response('Report details are  not Updated', $status, false);
                }

            } else {
                return $this->response('Error Occured', $data, false);
            }

        } catch (\Throwable $th) {
            return $this->response('Something wrong.' . $th, $th, false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotificationReportTypes  $NotificationReportTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotificationReportTypes $NotificationReportTypes, $id)
    {
        if (NotificationReportTypes::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
