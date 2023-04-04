<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{

    public function sentNotification($data)
    {
        if ($data['instance_id'] && $data['access_token']) {
            $response = Http::withoutVerifying()->get(env('WHATSAPP_URL'), [
                'number' => $data['to'],
                'type' => 'text',
                'message' => $data['message'],
                'instance_id' => env($data['instance_id']),
                'access_token' => env($data['access_token']),
            ]);

            return $response->status();
        }
    }
}
