<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExternalUrlController extends Controller
{
    public function whatsappCall()
    {

        $response = Http::withoutVerifying()->timeout(1000 * 120)->get("http://localhost:7733/send-message?phone=971554501483&message=Good");

        return $response->json();
    }



    public function sendMessage()
    {
        // API endpoint URL
        $url = 'https://demo.betablaster.in/api/send';

        // Data to send in the request
        $data = [
            'number' => request("number"),
            'type' => 'text',
            'message' => request("message"),
            'instance_id' => '674973D1CE41D',
            'access_token' => '67496f1b26e95',
        ];

        // Sending POST request using Http facade
        $response = Http::post($url, $data);

        // Handling the response
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'data' => $response->json(),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response->body(),
            ], $response->status());
        }
    }
}
