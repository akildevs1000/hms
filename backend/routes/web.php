<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyIfLogsDoesNotGenerate;

Route::redirect('/', 'api/test');


Route::get('/notifyFailure', function () {

    $data = [
        'title' => 'Introduction',
        'body' => 'this is from akil security system',
    ];

    Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new NotifyIfLogsDoesNotGenerate($data));
});



Route::post('/test1', function () {
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