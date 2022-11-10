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
