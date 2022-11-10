<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Notification;

class NotificationsController extends Controller
{
    public static function toSend($model, $notificationClass, $object)
    {
        Notification::send($model, new $notificationClass($object));
    }
}
