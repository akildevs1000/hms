<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function sentNotification($data)
    {
        try {
            if ($data['instance_id']) {
                $response = Http::withoutVerifying()->get(env('WHATSAPP_URL'), [
                    'number'       => $data['to'],
                    'type'         => 'text',
                    'message'      => $data['message'],
                    'instance_id'  => $data['instance_id'],
                    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
                ]);

                $msg = 'company Id: ' . $data['company']['id'] . ' Rev. No: ' . $data['revNo'] . ' '
                    . $data['type'] . ' from: ' . $data['to'];

                if ($response->status() == 200) {
                    Log::channel('whatsapp_logs')->info($msg);
                }
            }
        } catch (\Throwable $th) {
            Log::channel("custom")->error("BookingController: " . $th);
        }
    }

    public function sentOTP($data)
    {
        try {
            if ($data['instance_id']) {
                $response = Http::withoutVerifying()->get(env('WHATSAPP_URL'), [
                    'number'       => $data['to'],
                    'type'         => 'text',
                    'message'      => $data['message'],
                    'instance_id'  => env('OTP_INSTANCE_ID'),
                    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
                ]);

                $msg = 'company Id: ' . $data['company']['id'] . ' User Name : ' . $data['userName'] . ' '
                    . $data['type'] . ' from: ' . $data['to'];

                // dd($response->status());

                if ($response->status() == 200) {
                    Log::channel('whatsapp_logs')->info($msg);
                }
            }
        } catch (\Throwable $th) {
            Log::channel("custom")->error("BookingController: " . $th);
        }
    }

    // public function sentNotification($data)
    // {
    //     if ($data['instance_id'] && $data['access_token']) {
    //         $response = Http::withoutVerifying()->get(env('WHATSAPP_URL'), [
    //             'number' => $data['to'],
    //             'type' => 'text',
    //             'message' => $data['message'],
    //             'instance_id' => env($data['instance_id']),
    //             'access_token' => env($data['access_token']),
    //         ]);

    //         return $response->status();
    //     }
    // }
}
