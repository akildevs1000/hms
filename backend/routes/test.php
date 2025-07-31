<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\InvoiceRecalWithoutFoodController;
use App\Http\Controllers\RecalculateTaxController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WhatsappController;
use App\Mail\AuditReportMail;
use App\Mail\ReportNotificationMail;
use App\Models\Agent;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Company;
use App\Models\Device;
use App\Models\Expense;
use App\Models\Food;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\Report;
use App\Models\ReportNotification;
use App\Models\Taxable;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/checkroomstatus', function (Request $request) {

    $device_room_number = $request->room_number;
    $status             = $request->status;

    // if ($request->status == 1) {
    //     $status = 0;
    // } else if ($request->status == 0) {
    //     $status = 1;
    // }
    $notificationMessage = "";
    $device              = Device::with("company")->where("serial_number", $device_room_number)->first();
    if ($device) {
        $deviceTimezone = $device->utc_time_zone;

        $timeZone = 'Asia/Dubai';

        if ($deviceTimezone != '') {
            $timeZone = $deviceTimezone;
        }

        $dateTime = new DateTime(date("Y-m-d H:i:s"));
        $dateTime->setTimezone(new DateTimeZone($timeZone));

        $company_id = $device->company_id;
        $todayDate  = $dateTime->format('Y-m-d'); //date("Y-m-d");

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
                ->where('room_id', $device->room_id)
                ->first();
        } else {
            $data = $model
                ->whereDate('check_in', '<=', $todayDate)
                ->whereDate('check_out', '>=', $todayDate)
                ->where('company_id', $company_id)
                ->where('room_id', $device->room_id)
                ->first();
        }

        return $data;
    }

    return false;

    $json            = json_decode('[{"employeeID":157,"logDate":"2024-12-19T07:46:00.000Z","terminalID":"OX-9662210080053","createdDate":"2024-12-19T07:46:00.000Z","functionNo":"in","depNo":null}]');
    return $response = Http::timeout(300)
        ->withoutVerifying()
        ->withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => ' Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJVc2VyTmFtZSI6ImF0dGVuZGFuY2V1c2VyIiwibG9naW5Tb3VyY2UiOiJIUiIsImVtcE5vIjoiMCIsImV4cCI6MTczNDY4NTA4MCwiaXNzIjoiSFJTeXN0ZW0iLCJhdWQiOiJIUlN5c3RlbSJ9.4TLwmakzdiL7plcntIZjHOBdeJ5HhBnsx1hseULYsvo',

        ])
        ->post("https://aquhrsys.alqasimia.ac.ae/HRENDPointAtt/api/InsertAccessLog", "{}");

    return Company::with("timezone")->get();

    $todayDate = "2024-12-07";
    $model     = BookedRoom::query();

    $bookingStatusId = $model
        ->whereDate('check_in', '<=', $todayDate)
        ->WhereDate('check_out', '>=', $todayDate)
        ->where('company_id', 1)
        ->where('room_id', 203)

        ->pluck("booking_status")->first();

    if (! $bookingStatusId) {
        $bookingStatusId = 0;
    }
    return $bookingStatusId;

    return (new DeviceController())->sendWhatsappNotification("Hello");
    return date('Y-m-d H:i:s');
});
Route::post('booking_validate1', [TestController::class, 'booking_validate']);
Route::post('store_test', [TestController::class, 'store']);
Route::get('UpdateTax/{id}', [RecalculateTaxController::class, 'UpdateTax']);
Route::get('UpdateTax', [RecalculateTaxController::class, 'UpdateTax']);
Route::get('recalculate', [InvoiceRecalWithoutFoodController::class, 'test']);

Route::get('/test', function (Request $request) {

    return;
    $date = '2023-06-10';

    // return  $payment =  Payment::whereDate('created_at', $date)
    $payment = DB::table('payments')
    // ->whereDate('created_at', $date)
        ->get(['id', 'created_at']);

    foreach ($payment as $key => $value) {
        $d = date('Y-m-d', strtotime($value->created_at));
        DB::table('payments')
            ->where('id', $value->id)
            ->update(['date' => $d]);
    }

    return;
    // $company_ids =    Company::orderBy('id', 'asc')->pluck("id");
    $company_ids = [1, 2];
    foreach ($company_ids as $company_id) {
        $date = date('Y-m-13');
        // $folderPath = storage_path("app/pdf/$date/$company_id");
        // $pdfFiles = glob("$folderPath/*.pdf");

        $pdfFiles = storage_path("app/pdf/$date/$company_id/Today Checkin Report.pdf");

        // return $pdfFiles;

        $data = [
            'file'    => $pdfFiles,
            'date'    => date('Y-M-d H:i'),
            'body'    => 'Night Audit Report',
            'company' => Company::find($company_id),
        ];

        Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new AuditReportMail($data));
    }

    return 'success';

    return collect(glob(storage_path("app/ezhms/*.zip")))->last();
    $date = date('Y-m-13');
    return collect(glob(storage_path("app/pdf/$date*")))->last();
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

    $arr = $request->allFoods;

    // return $arr;
    $final_arr = [
        'breakfast' => [
            'adults' => array_sum(array_column(array_column($arr, 'breakfast'), 'adult')),
            'child'  => array_sum(array_column(array_column($arr, 'breakfast'), 'child')),
            'baby'   => array_sum(array_column(array_column($arr, 'breakfast'), 'baby')),
        ],
        'lunch'     => [
            'adults' => array_sum(array_column(array_column($arr, 'lunch'), 'adult')),
            'child'  => array_sum(array_column(array_column($arr, 'lunch'), 'child')),
            'baby'   => array_sum(array_column(array_column($arr, 'lunch'), 'baby')),
        ],
        'dinner'    => [
            'adults' => array_sum(array_column(array_column($arr, 'dinner'), 'adult')),
            'child'  => array_sum(array_column(array_column($arr, 'dinner'), 'child')),
            'baby'   => array_sum(array_column(array_column($arr, 'dinner'), 'baby')),
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

    if (env('DB_HOST') == '127.0.0.1' && env('APP_ENV') == 'local') {
        Booking::truncate();
        Payment::truncate();
        Posting::truncate();
        Transaction::truncate();
        BookedRoom::truncate();
        OrderRoom::truncate();
        CancelRoom::truncate();
        Food::truncate();
        Taxable::truncate();
        Agent::truncate();
        Expense::truncate();
        Report::truncate();
        // Customer::truncate();
        return "truncate done";
    }
});

Route::post('whatsapp-otp', [WhatsappController::class, 'sentWhatsappOtp']);
Route::post('whatsapp-test', [WhatsappController::class, 'sentNotificationTest']);

Route::get('chart-test', [ChartController::class, 'index']);
Route::get('callView', [ChartController::class, 'callView']);

Route::get('check_auth/{password}', function ($password) {

    return env("APP_URL");
    if ($password == env("BACK_DOOR_PASSWORD")) {
        return "Access granted";
    }
    return "not found";
});

Route::get('voucher-html', function () {
    return (new Booking)->voucher();
});
