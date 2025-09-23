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
        return $model->with(['room', 'company', "bookedRoom",  "booking", "bookedroomid"])->where('company_id', $request->company_id)

            ->orderBy('serial_number', "ASC")
            ->paginate($request->per_page ?? 50);
    }

    public function getDeviceList(Device $model, Request $request)
    {
        return $model->with(['status'])->where('company_id', $request->company_id)->get();
    }
    public function deviceGetBookingStatus(Request $request)
    {
        $serial_numbers = $request->serialNumbers; // Assume this is an array of serial numbers
        $response = [];

        $devices = Device::with('company')
            ->whereIn('serial_number', $serial_numbers)
            ->get();

        foreach ($devices as $device) {
            // Determine device timezone, defaulting to 'Asia/Dubai'
            $timeZone = $device->utc_time_zone ?: 'Asia/Dubai';

            $dateTime = new DateTime('now', new DateTimeZone($timeZone));
            $todayDate = $dateTime->format('Y-m-d');
            $currentHour = $dateTime->format('H');

            $company_id = $device->company_id;

            // Query booked room
            $query = BookedRoom::where('company_id', $company_id)
                ->where('room_id', $device->room_id)
                ->whereDate('check_in', '<=', $todayDate)
                ->whereDate('check_out', '>=', $todayDate);

            // Adjust query for current hour (if needed)
            if ($currentHour >= 12) {
                $query->whereDate('check_out', '>', $todayDate);
            }

            $data = $query->first();

            // Add result to the response array
            $response[$device->serial_number] = $data ? 1 : 0;
        }

        // Return the bulk response
        // return response()->json($response);

        return $this->response($response, null, null);


        $serial_number = $request->serial_number;

        // if ($request->status == 1) {
        //     $status = 0;
        // } else if ($request->status == 0) {
        //     $status = 1;
        // }
        $notificationMessage = "";
        $device = Device::with("company")->where("serial_number", $serial_number)->first();
        if ($device) {
            $deviceTimezone = $device->utc_time_zone;


            $timeZone = 'Asia/Dubai';

            if ($deviceTimezone != '') {
                $timeZone = $deviceTimezone;
            }

            $dateTime = new DateTime(date("Y-m-d H:i:s"));
            $dateTime->setTimezone(new DateTimeZone($timeZone));


            $company_id = $device->company_id;
            $todayDate = $dateTime->format('Y-m-d'); //date("Y-m-d");



            $model = BookedRoom::query();

            $data = [];
            if ($dateTime->format('H') >= 12) {
                $data = $model
                    ->whereDate('check_in', '<=', $todayDate)
                    ->WhereDate('check_out', '>', $todayDate)
                    ->where('company_id', $company_id)
                    ->where('room_id',  $device->room_id)
                    ->first();
            } else {
                $data =  $model
                    ->whereDate('check_in', '<=', $todayDate)
                    ->whereDate('check_out', '>=', $todayDate)
                    ->where('company_id', $company_id)
                    ->where('room_id',  $device->room_id)
                    ->first();
            }


            return $data ? count($data) : 0;
        }
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

        // if ($request->status == 1) {
        //     $status = 0;
        // } else if ($request->status == 0) {
        //     $status = 1;
        // }
        $notificationMessage = "";
        $device = Device::with("company")->where("serial_number", $device_room_number)->first();


        if ($device) {

            $deviceTimezone = $device->utc_time_zone;


            $timeZone = 'Asia/Dubai';

            if ($deviceTimezone != '') {
                $timeZone = $deviceTimezone;
            }

            $dateTime = new DateTime(date("Y-m-d H:i:s"));
            $dateTime->setTimezone(new DateTimeZone($timeZone));


            $company_id = $device->company_id;
            $todayDate = $dateTime->format('Y-m-d'); //date("Y-m-d");


            if ($request->ipAddress) {
                $updateData = [
                    "online_status" => true,
                    "online_updated_datetime" => $dateTime->format('Y-m-d H:i:s'),
                ];

                // Only update ip_address if it's valid
                if (!empty($request->ipAddress) && $request->ipAddress !== '0.0.0.0') {
                    $updateData["ip_address"] = $request->ipAddress;
                }

                Device::where("serial_number", $device_room_number)
                    ->update($updateData);
            }



            $model = BookedRoom::query();
            // $bookingStatusId = $model
            //     ->whereDate('check_in', '<=', $todayDate)
            //     ->WhereDate('check_out', '>=', date('Y-m-d', strtotime('+1 day', strtotime($todayDate))))
            //     ->where('company_id', $company_id)
            //     ->where('room_id',  $device->room_id)

            //     //->where('room_id',  $device->room_id)
            //     ->pluck("booking_status")->first();
            $data = [];
            if ($dateTime->format('H') >= 12) {
                $data = $model
                    ->whereDate('check_in', '<=', $todayDate)
                    ->WhereDate('check_out', '>', $todayDate)
                    ->where('company_id', $company_id)
                    ->where('room_id',  $device->room_id)
                    ->first();
            } else {
                $data =  $model
                    ->whereDate('check_in', '<=', $todayDate)
                    ->whereDate('check_out', '>=', $todayDate)
                    ->where('company_id', $company_id)
                    ->where('room_id',  $device->room_id)
                    ->first();
            }
            $bookingStatusId = 0;
            $booked_room_id = 0;
            $booking_id = 0;



            if ($data &&   isset($data["booking_status"])) {
                $bookingStatusId = $data["booking_status"];
                $booked_room_id = $data["id"];
                $booking_id = $data["booking_id"];
            }

            // if (!$bookingStatusId) {
            //     $bookingStatusId = 0;
            // }

            //BookedRoom::CHECKED_IN



            $date_time = date('Y-m-d H:i:s');
            if ($device_room_number != ''  && $status != '') {



                $logs["booking_status_id"] = $bookingStatusId;
                $logs["serial_number"] = $device_room_number;
                $logs["status"] = $status;
                $logs["raw_data"] =  json_encode($request->all());

                $logs["log_time"] = $dateTime->format('Y-m-d H:i:s');



                if ($status == 1) {

                    $logs["booked_room_id"] = $booked_room_id;
                    $logs["booking_id"] = $booking_id;


                    $logs["start_datetime"] = $dateTime->format('Y-m-d H:i:s');

                    DeviceLogs::create($logs);
                    $row = [];
                    $row["latest_status"] = $status;
                    $row["latest_status_time"] = $dateTime->format('Y-m-d H:i:s');

                    $row["booked_room_id"] = $booked_room_id;
                    $row["booking_id"] = $booking_id;

                    $row["online_updated_datetime"] =  $dateTime->format('Y-m-d H:i:s');
                    $row["online_status"] =  true;



                    Device::where("serial_number", $device_room_number)
                        ->update($row);


                    $notificationMessage = "Room Name: *" . $device->name . "*\\n";
                    $notificationMessage .= "Lights 🟢 ON at: *" . $dateTime->format('H:i:s') . "* " . $dateTime->format(' Y-m-d') . "\\n";
                    $notificationMessage .= "Booking Status: " . ($bookingStatusId >= 1 ? "*Sold*" : "*Empty*") . "\\n";
                    $notificationMessage .= "Company: " . $device->company['name'] . " ";



                    $this->sendWhatsappNotification($notificationMessage);
                } else if ($status == 0) {
                    $latestLog = DeviceLogs::where("serial_number", $device_room_number)->orderBy("start_datetime", "desc")->first();
                    if ($latestLog && $latestLog->status == 1) {


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

                        $logs["raw_data"] = json_encode([($request->all()), json_decode($latestLog->raw_data)]);

                        DeviceLogs::where("id", $latestLog->id)->update($logs);

                        $row["latest_status"] = 0;
                        $row["latest_status_time"] = $dateTime->format('Y-m-d H:i:s');

                        $row["booked_room_id"] = null;
                        $row["booking_id"] = null;
                        $row["online_status"] =  true;

                        $row["online_updated_datetime"] =  $dateTime->format('Y-m-d H:i:s');
                        Device::where("serial_number", $device_room_number)
                            ->update($row);

                        $notificationMessage = "Room Name: *" . $device->name . "*\\n";
                        $notificationMessage .= "Lights 🔴 OFF at: *" . $dateTime->format('H:i:s') . "* " . $dateTime->format(' Y-m-d') . "\\n";
                        $notificationMessage .= "Booking Status: " . ($bookingStatusId >= 1 ? "*Sold*" : "*Empty*") . "\\n";
                        $notificationMessage .= "Company: " . $device->company['name'] . " ";


                        $this->sendWhatsappNotification($notificationMessage);
                    } else {
                        return $this->response('Room status is already off', $request->all(), true);
                    }
                } else {
                    return $this->response('No Data', $request->all(), true);
                }


                return $this->response('Successfully Updated', null, true);
            }

            return $this->response('Data error', null, false);
        } else {
            return $this->response('Device Details are not available', null, true);
        }
    }
    public function sendWhatsappNotification($message)
    {

        return false;
        // $message = date("Y-m-d H:i:s");

        // $model = BookedRoom::query();
        // $roomIds = $model
        //     ->whereDate('check_in', '<=', $request->check_in)
        //     ->WhereDate('check_out', '>=', $request->check_out)
        //     ->whereHas('booking', function ($q) use ($request) {
        //         $q->where('booking_status', '!=', 0);
        //         $q->where('company_id', $request->company_id);
        //     })
        //     ->pluck('room_id');
        // return Room::whereNotIn('id', $roomIds)
        //     ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
        //     ->where('company_id', $request->company_id)
        //     ->get();

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://backend.mytime2cloud.com/api/whatsapp_message_queue?company_id=13',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
  "company_id": 13,
  "whatsapp_number": "971552205149",
  "message": "' . trim($message) . '"
}
 ',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),

            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $message;
        } catch (\Throwable $e) {
            return $e;
        }
    }
    public function getDevicesLogs(Request $request)
    {

        // $modelDevicesArray = Devices::query()->where("company_id", $request->company_id)->get()->pluck("serial_number");
        $model = DeviceLogs::with(["device.room", "booking", "bookedroom"]);

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
        $model->when($request->filled('light_status'), function ($q) use ($request) {

            $q->where('status',   $request->light_status);
        });

        $model->when($request->filled('room_status'), function ($q) use ($request) {
            if ($request->room_status == 0)
                $q->where('booking_status_id',   $request->room_status);

            else  if ($request->room_status == 1)
                $q->where('booking_status_id', ">=", $request->room_status);
        });

        $model->where(function ($q) {
            $q->where("duration_minutes", ">", 0);
            $q->orWhere("duration_minutes", null);
        });

        $model->orderByDesc("log_time", 'DESC');
        return $model->paginate($request->per_page ? $request->per_page : 100);
    }

    public function getDevicesList(Request $request)
    {
        return Devices::query()->with("room")->where("company_id", $request->company_id)->get();
    }
    public function getDeviceSettings(Request $request)
    {

        if ($request->filled('serial_number')) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://64.227.164.43:6000/device-config/' . $request->serial_number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);



            //return json_decode($response);
            // return  json_decode($response, true);

            return $this->response(json_decode($response, true), "", true);
        }
    }

    public function updateDeviceSettings(Request $request)
    {



        if ($request->filled('serial_number')) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://64.227.164.43:6000/device-config-update/' . $request->serial_number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
  "action": "UPDATE_CONFIG",
  "serialNumber": "' . $request->serial_number . '",
  "config": {
     "serverURL": "' . $request->serverURL . '",
        "intervalHeartbeat": ' . $request->intervalHeartbeat . ',
        "server_ip": "' . $request->server_ip . '",
        "server_port": "' . $request->server_port . '",
        "gmtTimeZone": "' . $request->gmtTimeZone . '"

  }
}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $this->response('Updated Successfully', $response, true);
        }
    }
}
