<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailNotifications\StoreEmailNotificationsRequest;
use App\Http\Requests\EmailNotifications\UpdateEmailNotificationsRequest;
use App\Models\EmailNotifications;
use Illuminate\Http\Request;

class EmailNotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = EmailNotifications::query();

        //datatable Filters
        if ($request->filled('email')) {
            $model->where('email', 'like', "$request->email%");}

        if ($request->filled('status')) {
            $model->where('status', $request->status);
        }
        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';
            $model->orderBy($request->sortBy, $sortDesc);

        } else {
            $model->orderBy('email', 'ASC');
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
     * @param  \App\Http\Requests\StoreEmailNotificationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $data = $request->validated();
        try {
            $data = $request->all();
            $dataArray = json_decode($data['list']);
            foreach ($dataArray as $value) {
                $verifyIsEmail = EmailNotifications::where('company_id', $request->company_id)->where('email', $value->email)->count();
                if ($verifyIsEmail == 0) {

                    //$value['company_id'] = $request->company_id;

                    $record = EmailNotifications::create(['company_id' => $request->company_id, 'email' => $value->email, 'status' => $value->status]);

                }
            }

            return $this->response('Email details are successfully created', null, true);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function storeSingle(StoreEmailNotificationsRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsRoom = EmailNotifications::where('company_id', $request->company_id)->where('email', $request->email)->count();
                if ($verifyIsRoom == 0) {

                    $record = EmailNotifications::create($data);

                    if ($record) {
                        return $this->response('Email details are successfully created', $record, true);
                    } else {
                        return $this->response('Email details not created', $record, false);
                    }

                } else {
                    return $this->response($request->email . ' : Email is already exist. ', $data, false);
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
     * @param  \App\Models\EmailNotifications  $emailNotifications
     * @return \Illuminate\Http\Response
     */
    public function show(EmailNotifications $emailNotifications, $id)
    {
        return EmailNotifications::where('id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailNotifications  $emailNotifications
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailNotifications $emailNotifications)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmailNotificationsRequest  $request
     * @param  \App\Models\EmailNotifications  $emailNotifications
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmailNotificationsRequest $request, EmailNotifications $emailNotifications, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isRoomExist = EmailNotifications::where('email', $request->email)
                    ->where('company_id', $request->company_id)
                    ->first();

                if ($isRoomExist) {
                    if ($isRoomExist->id != $id) {
                        return $this->response($request->email . 'Email is already Exist', null, false);
                    }
                }
                $status = EmailNotifications::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Email is updated succesfully', $status, true);
                } else {
                    return $this->response('Email is not Updated', $status, false);
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
     * @param  \App\Models\EmailNotifications  $emailNotifications
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailNotifications $emailNotifications, $id)
    {
        if (EmailNotifications::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
