<?php

use App\Models\User;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\OrderRoom;
use App\Mail\DbBackupMail;
use App\Models\Attendance;
use App\Models\BookedRoom;
use Illuminate\Http\Request;
use App\Models\ReportNotification;
use Illuminate\Support\Facades\DB;
use App\Mail\ReportNotificationMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Hash;

Route::post('/test', function (Request $request) {



    return Hash::make('DE6aMuM#');
    return date('Y-m-d H:i:s');

    return is_numeric(111);
    try {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/102482416002121/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "messaging_product": "whatsapp",
    "to": "923108559858",
    "type": "template",
    "template": {
        "name": "hello_world",
        "language": {
            "code": "en_US"
        }
    }
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer EAAMGxA8O290BAGKRYuhwVdA1GAgpZB7UnlKF8kgIGdieVXQotF51BavjJ7t8ckHyP4dpwHmDGJULscjCdgs6k4d5Y81McR5q86D5XfG489CPS1qpCJPe7kLW9ltFx0sRVMxiCw7VYkvFybjQ6lrCNHR0ce86p9KHQKz2UpJyEVHX5EcVW997dL5ZBkskbTctzWyDE6z0oXOKHXGKZAD',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    } catch (\Throwable $th) {
        throw $th;
    }

    // $data = [
    //     'file' => collect(glob(storage_path("app/ideaHrms/*.zip")))->last(),
    //     'date' => date('Y-M-d'),
    //     'body' => 'ideahrms Database Backup',
    // ];

    // Mail::to('fahathammex90@gmail.com')
    //     ->queue(new DbBackupMail($data));
});




Route::get('/reset_attendance', [AttendanceController::class, 'ResetAttendance']);


Route::get('/generate_attendance_log', function (Request $request) {

    $arr = [];
    for ($i = 1; $i <= 5; $i++) {
        for ($j = 13; $j <= 13; $j++) {
            for ($k = 1; $k <= 1; $k++) {
                $time =  rand(8, 20);
                $time = $time < 10 ? '0' . $time : $time;
                $arr[] = [
                    'UserID' => $i,
                    'LogTime' => "2022-10-$j $time:00:00",
                    'DeviceID' => "OX-8862021010097",
                    'company_id' => "1",
                ];
            }
        }
    }
    // return $arr;
    DB::table('attendance_logs')->insert($arr);
});

Route::get('/test-re', function (Request $request) {
    Employee::truncate();
    DB::statement('DELETE FROM users WHERE id > 2');

    return 'done';
});

Route::get('/test-date', function (Request $request) {

    // $start = date('Y-m-d');
    // $end = date('Y-m-d');

    $start = date('Y-m-1'); // hard-coded '01' for first day
    $end = date('Y-m-t');

    $model = Attendance::query();
    return $model->whereBetween('date', [$start, $end])
        ->get();

    return 'done';
});

Route::get('/storage', function (Request $request) {
    Storage::put('example.csv', 'francis');
});

Route::post('/upload', function (Request $request) {
    $file = $request->file->getClientOriginalName();
    $request->file->move(public_path('media/employee/file/'), $file);
    return $product_image = url('media/employee/file/' . $file);
    $data['file'] = $file;
});

Route::get('/test/whatsapp', function () {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.facebook.com/v14.0/102482416002121/messages',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "messaging_product": "whatsapp",
    "to": "923108559858",
    "type": "template",
    "template": {
        "name": "hello_world",
        "language": {
            "code": "en_US"
        }
    }
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer EAAP9IfKKSo0BALkTWKQE6xLcyfO3eyGt69Y7SH6EfpCmKCAGb1AZCuptzmnPf5qsRZBaj4WYqSXbbxDEvaOD6WiiFwklq4P0FvASsBYOigDTrEhC3geXTNLFZCzQ1wTxNthkfzI4wSfG0KF79rrvh7cEIKdyx7mvM4ZC06MHNZBYg78yYrfGZCIcbtDUnegflDudZB5e2i9AZBDCIJ81o2xa'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
});

Route::get('/test_attachment', function () {

    $models = ReportNotification::get();

    foreach ($models as $model) {

        return $model;

        if ($model->frequency == "Daily") {
            if (in_array("Email", $model->mediums)) {
                Mail::to($model->tos)
                    ->cc($model->ccs)
                    ->bcc($model->bccs)
                    ->queue(new ReportNotificationMail($model));
            }
            // if (in_array("Whatsapp", $model->mediums)) {
            //     Mail::to($model->tos)->send(new TestMail($model));
            // }
        }
    }
    return "done";
});
Route::get('/my_test', function () {

    // All countries
    // length 252

    $booked = " ";
    if ($booked) {
        return '1';
    }
    return 'done';


    $rooms =    [
        [
            'room_no' => '102',
            'room_type' => 'king',
            'room_id' => 2,
            'price' => '3800',
            'days' => 4,
            'sgst' => 342,
            'cgst' => 342,
            'check_in' => '2022-12-17',
            'check_out' => '2022-12-20',
            'meal' => 'Room only',
            'bed_amount' => 0,
            'room_discount' => 0,
            'after_discount' => 3800,
            'room_tax' => 684,
            'total_with_tax' => 4484,
            'total' => 4484,
            'grand_total' => 17936,
            'company_id' => 1,
            'booking_id' => 68,
            'customer_id' => 116,
        ],

        [
            'check_in' => '2022-12-17',
            'room_discount' => 0,
            'check_out' => '2022-12-20',
            'meal' => 'Room only',
            'company_id' => 1,
            'room_no' => '305',
            'room_id' => 15,
            'room_type' => 'queen',
            'price' => '2800',
            'after_discount' => 2800,
            'days' => 4,
            'room_tax' => 336,
            'total_with_tax' => 3136,
            'cgst' => 168,
            'sgst' => 168,
            'total' => 3136,
            'grand_total' => 12544,
            'booking_id' => 68,
            'customer_id' => 116,
        ]
    ];


    $dates = [];

    foreach ($rooms as $room) {
        // return $room;
        $period = CarbonPeriod::create($room['check_in'], $room['check_out']);

        foreach ($period as $date) {
            // return $date->format('Y-m-d');
            $room['date'] = $date->format('Y-m-d');
            // return $room;
            OrderRoom::create($room);
        }
        // return  $dates; //= $period->toArray();

    }
    return;

    $period = CarbonPeriod::create('2018-06-14', '2018-06-20');
    $dates = [];
    // Iterate over the period
    foreach ($period as $date) {
        $dates[] = $date->format('Y-m-d');
    }

    // Convert the period to an array of dates
    return  $dates; //= $period->toArray();
});