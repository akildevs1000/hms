<?php

namespace App\Http\Controllers;

use App\Mail\ActionMarkdownMail;
use App\Models\BookedRoom;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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

    function testPdf()
    {
        try {
            // Send the test email

            $email = "francisgill1000@gmail.com";

            Mail::to($email)->send(new ActionMarkdownMail(
                'This is the body text of the test email.',
                'Test Email Subject',
                49
            ));

            return 'Test email sent successfully to ' . $email;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }

    public function sandBox($company_id = 0)
    {
        $todayDate = date("Y-m-d");
        $FoodOrder = BookedRoom::where('company_id', $company_id)
            ->where(function ($query) use ($todayDate) {
                $query->whereDate('check_out', $todayDate)
                    ->orWhereDate('check_in', $todayDate);
            })
            ->whereIn('booking_status', [BookedRoom::CHECKED_IN])
            ->selectRaw("
        SUM(CASE WHEN DATE(check_in) = ? THEN breakfast ELSE 0 END) as occupied_breakfast,
        SUM(CASE WHEN DATE(check_in) = ? THEN lunch ELSE 0 END) as occupied_lunch,
        SUM(CASE WHEN DATE(check_in) = ? THEN dinner ELSE 0 END) as occupied_dinner
    ", [$todayDate, $todayDate, $todayDate])
            ->first();

        $occupiedBreakfast = $FoodOrder->occupied_breakfast ?? 0;
        $occupiedLunch = $FoodOrder->occupied_lunch ?? 0;
        $occupiedDinner = $FoodOrder->occupied_dinner ?? 0;

        $foodOrdersCount = [
            "breakfast" => $occupiedBreakfast,
            "lunch" => $occupiedLunch,
            "dinner" => $occupiedDinner,
            "total" => $occupiedBreakfast + $occupiedLunch + $occupiedDinner,
        ];

        return $foodOrdersCount;
    }
}
