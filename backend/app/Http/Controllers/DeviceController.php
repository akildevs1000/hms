<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\Device\StoreRequest;
use App\Http\Requests\Device\UpdateRequest;
use App\Models\AttendanceLog;
use App\Models\BookedRoom;
use App\Models\DeviceLogs;
use App\Models\Devices;
use App\Models\Room;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function index(Device $model, Request $request)
    {
        return $model->with(['room', 'company'])->where('company_id', $request->company_id)->paginate($request->per_page ?? 1000);
    }

    public function getDeviceList(Device $model, Request $request)
    {
        return $model->with(['status'])->where('company_id', $request->company_id)->get();
    }

    public function store(Device $model, StoreRequest $request)
    {

        if ($request->validated()) {
            try {


                $record = $model->create($request->validated());

                if ($record) {
                    return $this->response('Device successfully added.', $record, true);
                } else {
                    return $this->response('Device cannot add.', null, 'device_api_error');
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    public function show(Device $model, $id)
    {
        return $model->with(['status', 'company'])->find($id);
    }

    public function getDeviceCompany(Request $request)
    {
        $model = DB::table("employees")->where("system_user_id", $request->UserCode)->first(['first_name', 'profile_picture']);

        if ($model && $model->profile_picture) {
            $model->profile_picture = asset('media/employee/profile_picture/' . $model->profile_picture);
        }

        return [
            "UserID" => $request->UserCode,
            "time" => date("H:i", strtotime($request->RecordDate)),
            "employee" => $model,
            "device" => DB::table("devices")->where("device_id", $request->DeviceID)->first(['name as device_name', 'short_name', 'device_id', 'location', "company_id"]),
        ];
    }

    public function getLastRecordsByCount($id, $count)
    {
        $model = AttendanceLog::query();
        $model->where('company_id', $id);
        $model->take($count);
        $model->orderByDesc("id");
        $model->with([
            "device:id,company_id,name as device_name,short_name,device_id,location",
            "employee:id,first_name,profile_picture,system_user_id",
        ]);
        return $model->get();


        // Cache::forget("last-five-logs");
        return Cache::remember('last-five-logs', 300, function () use ($id, $count) {

            $model = AttendanceLog::query();
            $model->where('company_id', $id);
            $model->take($count);
            $model->orderByDesc("id");
            $model->with([
                "device:id,company_id,name as device_name,short_name,device_id,location",
                "employee:id,first_name,profile_picture,system_user_id",
            ]);
            return $model->get();
        });
    }

    public function update(Device $Device, UpdateRequest $request)
    {
        try {
            $record = $Device->update($request->validated());

            if ($record) {
                return $this->response('Device successfully updated.', $record, true);
            } else {
                return $this->response('Device cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy(Device $device)
    {
        try {
            $record = $device->delete();

            if ($record) {
                return $this->response('Device successfully deleted.', $record, true);
            } else {
                return $this->response('Device cannot delete.', null, 'device_api_error');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function search(Request $request, $key)
    {
        $model = Device::query();

        $fields = [
            'name',
            'device_id',
            'location',
            'short_name',
            'status' => ['name'],
            'company' => ['name'],
        ];

        $model = $this->process_search($model, $key, $fields);

        $model->with(['status', 'company']);

        return $model->paginate($request->per_page);
    }
    public function deleteSelected(Device $model, Request $request)
    {
        try {
            $record = $model->whereIn('id', $request->ids)->delete();

            if ($record) {
                return $this->response('Device successfully deleted.', $record, true);
            } else {
                return $this->response('Device cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateDevicRoomFileStatus(Request $request)
    {







        $device_room_number = $request->room_number;
        $status = $request->status;


        $device = Device::where("serial_number", $device_room_number)->first();
        if ($device) {
            $deviceTimezone = $device->utc_time_zone;

            $company_id = $device->company_id;
            $todayDate = date("Y-m-d");


            // $model = BookedRoom::query();
            // $roomIds = $model
            //     ->whereDate('check_in', '<=', $todayDate)
            //     ->WhereDate('check_out', '>=', $todayDate)
            //     ->whereHas('booking', function ($q) use ($company_id) {
            //         $q->where('booking_status', '!=', 0);
            //         $q->where('company_id', $company_id);
            //     })
            //     ->pluck('room_id');
            // Room::whereNotIn('id', $roomIds)
            //     ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            //     ->where('company_id', $company_id)
            //     //->where('room_no', $device_room_number)
            //     ->get();













            $date_time = date('Y-m-d H:i:s');
            if ($device_room_number != ''  && $status != '') {

                $logs["serial_number"] = $device_room_number;
                $logs["status"] = $status;
                $logs["raw_data"] = json_encode($request->all());

                $timeZone = 'Asia/Dubai';

                if ($deviceTimezone != '') {
                    $timeZone = $deviceTimezone;
                }

                $dateTime = new DateTime(date("Y-m-d H:i:s"));
                $dateTime->setTimezone(new DateTimeZone($timeZone));

                $logs["log_time"] = $dateTime->format('Y-m-d H:i:s');

                if ($status == 1) {

                    $logs["start_datetime"] = $dateTime->format('Y-m-d H:i:s');

                    DeviceLogs::create($logs);
                    $row = [];
                    $row["latest_status"] = $status;
                    $row["latest_status_time"] = $dateTime->format('Y-m-d H:i:s');

                    Device::where("serial_number", $device_room_number)
                        ->update($row);
                } else if ($status == 0) {
                    $latestLog = DeviceLogs::where("serial_number", $device_room_number)->orderBy("start_datetime", "desc")->first();
                    if ($latestLog->status == 1) {


                        $logs = [];
                        $logs["status"] = 0;
                        $logs["end_datetime"] = $dateTime->format('Y-m-d H:i:s');
                        // Define the two dates
                        $date1 = new DateTime($latestLog->start_datetime);
                        $date2 = new DateTime($logs["end_datetime"]);

                        // Calculate the difference
                        $interval = $date1->diff($date2);

                        // Convert the difference to minutes
                        $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

                        $logs["duration_minutes"] = $minutes;



                        DeviceLogs::where("id", $latestLog->id)->update($logs);

                        $row["latest_status"] = 0;
                        $row["latest_status_time"] = $dateTime->format('Y-m-d H:i:s');

                        Device::where("serial_number", $device_room_number)
                            ->update($row);
                    } else {
                        return $this->response('Room status is already off', $request->all(), true);
                    }
                } else {
                    return $this->response('No Data', $request->all(), true);
                }


                return $this->response('Successfully Updated', null, true);
            }

            return $this->response('Data error', null, false);
        }
    }

    public function getDevicesLogs(Request $request)
    {

        // $modelDevicesArray = Devices::query()->where("company_id", $request->company_id)->get()->pluck("serial_number");
        $model = DeviceLogs::with("device.room");

        $model->whereIn("serial_number", Devices::query()->where("company_id", $request->company_id)->get()->pluck("serial_number"));
        $model->when($request->filled('serial_number'), function ($q) use ($request) {
            $q->where('serial_number',   $request->serial_number);
        });
        // $model->when($request->filled('room_id'), function ($q) use ($request) {
        //     $q->where('room_id',   $request->room_id);
        // });

        $model->when($request->filled('from_date'), function ($q) use ($request) {
            $q->where('log_time',  ">=", $request->from_date . ' 00:00:00');
        });
        $model->when($request->filled('to_date'), function ($q) use ($request) {
            $q->where('log_time',  "<=", $request->to_date . ' 23:59:59');
        });

        $model->orderByDesc("log_time", 'DESC');
        return $model->paginate($request->per_page ? $request->per_page : 100);
    }

    public function getDevicesList(Request $request)
    {
        return Devices::query()->where("company_id", $request->company_id)->get();
    }
}
