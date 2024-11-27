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
}
