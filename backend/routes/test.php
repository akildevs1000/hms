<?php

use App\Models\Food;
use App\Mail\TestMail;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\Taxable;
use App\Models\Customer;
use App\Models\Employee;
use App\Jobs\WhatsappJob;
use App\Models\OrderRoom;
use App\Mail\DbBackupMail;
use App\Models\Attendance;
use App\Models\BookedRoom;
use App\Models\CancelRoom;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ReportNotification;
use Illuminate\Support\Facades\DB;
use App\Mail\ReportNotificationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\AttendanceController;

Route::post('booking_validate1', [TestController::class, 'booking_validate']);
Route::post('store_test', [TestController::class, 'store']);

Route::get('/test', function (Request $request) { {
        return   Customer::whereContactNo("null")->whereCompanyId(2)->first();
    }
    // THANJ_INSTANCE_ID
    // THANJ_ACCESS_TOKEN

    return Customer::customerAttributes();
    $data = [
        "from"         => "14157386102",
        "to"           => "971502848071",
        "message_type" => "text",
        "text"         => "This is a WhatsApp Message sent from the EZHMS",
        "channel"      => "whatsapp",
    ];

    (new WhatsappController)->toSendNotification($data);
    return 'done';
    WhatsappJob::dispatch($data);
    return;

    for ($i = 0; $i < 1000; $i++) {
        WhatsappJob::dispatch($data);
    }
});

Route::get('/db_backup', function (Request $request) {

    // $data = [
    //     'file' => collect(glob(storage_path("app/ezhms/*.zip")))->last(),
    //     'date' => date('Y-M-d H:i'),
    //     'body' => 'ezhms Database Backup',
    // ];

    // return Mail::to(env("ADMIN_MAIL_RECEIVERS"))->queue(new DbBackupMail($data));

    $data = [
        'file' => collect(glob(storage_path("app/ezhms/*.zip")))->last(),
        'date' => date('Y-M-d H:i'),
        'body' => 'ezhms Database Backup',
    ];

    Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new DbBackupMail($data));
});

Route::get('/reset_attendance', [AttendanceController::class, 'ResetAttendance']);

Route::get('/generate_attendance_log', function (Request $request) {

    $arr = [];
    for ($i = 1; $i <= 5; $i++) {
        for ($j = 13; $j <= 13; $j++) {
            for ($k = 1; $k <= 1; $k++) {
                $time  = rand(8, 20);
                $time  = $time < 10 ? '0' . $time : $time;
                $arr[] = [
                    'UserID'     => $i,
                    'LogTime'    => "2022-10-$j $time:00:00",
                    'DeviceID'   => "OX-8862021010097",
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
    $end   = date('Y-m-t');

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
    $data['file']         = $file;
});

Route::get('/test/whatsapp', function () {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://graph.facebook.com/v14.0/102482416002121/messages',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => '{
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
        CURLOPT_HTTPHEADER     => array(
            'Content-Type: application/json',
            'Authorization: Bearer EAAP9IfKKSo0BALkTWKQE6xLcyfO3eyGt69Y7SH6EfpCmKCAGb1AZCuptzmnPf5qsRZBaj4WYqSXbbxDEvaOD6WiiFwklq4P0FvASsBYOigDTrEhC3geXTNLFZCzQ1wTxNthkfzI4wSfG0KF79rrvh7cEIKdyx7mvM4ZC06MHNZBYg78yYrfGZCIcbtDUnegflDudZB5e2i9AZBDCIJ81o2xa',
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
Route::get('/my_test', function (Request $request) {

    $arr =   $request->allFoods;

    // return $arr;
    $final_arr = [
        'breakfast' => [
            'adults' => array_sum(array_column(array_column($arr, 'breakfast'), 'adult')),
            'child' => array_sum(array_column(array_column($arr, 'breakfast'), 'child')),
            'baby' => array_sum(array_column(array_column($arr, 'breakfast'), 'baby')),
        ],
        'lunch' => [
            'adults' => array_sum(array_column(array_column($arr, 'lunch'), 'adult')),
            'child' => array_sum(array_column(array_column($arr, 'lunch'), 'child')),
            'baby' => array_sum(array_column(array_column($arr, 'lunch'), 'baby')),
        ],
        'dinner' => [
            'adults' => array_sum(array_column(array_column($arr, 'dinner'), 'adult')),
            'child' => array_sum(array_column(array_column($arr, 'dinner'), 'child')),
            'baby' => array_sum(array_column(array_column($arr, 'dinner'), 'baby')),
        ],
    ];

    return $final_arr['breakfast'];
});

Route::get('remove_booking/{id}', function ($id) {

    return Hash::make($id);
    return;

    Booking::find($id)->delete();
    Payment::whereBookingId($id)->delete();
    Transaction::whereBookingId($id)->delete();
    BookedRoom::whereBookingId($id)->delete();
    OrderRoom::whereBookingId($id)->delete();
    CancelRoom::whereBookingId($id)->delete();
    Food::whereBookingId($id)->delete();

    return "removed booking";
});

Route::get('truncate', function () {

    // return now();
    Booking::truncate();
    Payment::truncate();
    Posting::truncate();
    Transaction::truncate();
    BookedRoom::truncate();
    OrderRoom::truncate();
    CancelRoom::truncate();
    Food::truncate();
    Taxable::truncate();
    Expense::truncate();
    // Customer::truncate();
    return "truncate done";
});