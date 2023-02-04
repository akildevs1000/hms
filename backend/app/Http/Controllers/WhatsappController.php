<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function api1($data)
    {
        $url = "https://messages-sandbox.nexmo.com/v1/messages";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Basic NmU3MzVjYzA6ZU5UeXd3N1BuMTcyM3RQSg==",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    public function toSendNotification($data)
    {
        return $this->api($data);
    }

    public function api($data)
    {
        $url = "https://messages-sandbox.nexmo.com/v1/messages";

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic NmU3MzVjYzA6ZU5UeXd3N1BuMTcyM3RQSg==',
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        if ($response->successful()) {
            $data = $response->body();
            Log::Info('this message from whatsapp notification', [$data]);
            return $data;
        }
        Log::Info('this message from whatsapp notification', [$response]);
        return 'something went wrong';
    }


    public function sentNotification($data)
    {
        $response = Http::withoutVerifying()->get(env('WHATSAPP_URL'), [
            'number' => $data['to'],
            'type' => 'text',
            'message' => $data['message'],
            'instance_id' => env($data['instance_id']),
            'access_token' => env($data['access_token']),
        ]);

        return $response->status();
        // dd($response->status());

        if ($response->successful()) {
            $data = $response->body();
            Log::Info('this message from whatsapp notification', [$data]);
            return $data;
        }
        Log::Info('this message from whatsapp notification', [$response]);
        return 'something went wrong';
    }
}
