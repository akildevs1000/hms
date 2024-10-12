<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/datetest', function (Request $request) {

    return date('Y-m-d H:i:s');
});
Route::post('booking_validate1', [TestController::class, 'booking_validate']);
Route::post('store_test', [TestController::class, 'store']);
Route::get('UpdateTax/{id}', [RecalculateTaxController::class, 'UpdateTax']);
Route::get('UpdateTax', [RecalculateTaxController::class, 'UpdateTax']);
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
            'file' => $pdfFiles,
            'date' => date('Y-M-d H:i'),
            'body' => 'Night Audit Report',
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
    $data['file'] = $file;
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


