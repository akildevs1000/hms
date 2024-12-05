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

    public function sandBox($company_id = 3)
    {
        $id = $company_id;
        $today = Carbon::tomorrow();



        $dates = [];

        for ($i = 0; $i < 1; $i++) {
           

            $date = date("Y-m-d", strtotime("+$i days", strtotime($today)));

            return $bookedData = BookedRoom::without("booking", "postings")
            ->orderBy("check_in")
            ->where(function ($q) use ($date) {
                $q->whereDate('check_in', ">=",  $date)
                    ->orWhereDate('check_out', "<=",  $date);
            })
            ->where('booking_status', BookedRoom::BOOKED)
            ->where('company_id', $id)
            ->get(["check_in", "check_out"]);



            $AvailableRooms = Room::with("is_cleaned")
            ->where('company_id', $id)
            ->whereNot("status", Room::Blocked)
            ->whereDoesntHave("bookedRoom", function ($query) use ($date, $id) {
                $query->where(function ($query) use ($date) {
                    $query->whereDate('check_in', ">=",  $date)
                        ->orWhereDate('check_in', "<=",  $date);
                })
                    ->where('company_id', $id);
            })
            ->count();

            $dates[$date] = [
                "label" => date("D", strtotime($date)),
                "bookedCount" => 0,
                "bookedPercent" => 0,
                "availableCount" => 0,
                "availablePercent" => 100,
            ];
            $bookedData = BookedRoom::without("booking", "postings")
                ->orderBy("check_in")
                ->where(function ($q) use ($today) {
                    $q->whereDate('check_in', ">=",  $today)
                        ->orWhereDate('check_out', "<=",  $today);
                })
                ->where('booking_status', BookedRoom::BOOKED)
                ->where('company_id', $id)
                ->get(["check_in", "check_out"]);
            $counter = 0;
            foreach ($bookedData as $book) {
                $check_in = $book->check_in;
                $check_out = $book->check_out;
                if ($date >= $check_in && $date <= $check_out) {
                    ++$counter;
                    $dates[$date] = [
                        "label" => date("D", strtotime($date)),
                        "bookedCount" => $counter,
                        "bookedPercent" => round(($counter / $AvailableRooms) * 100, 2),
                        "availableCount" => $AvailableRooms - $counter,
                        "availablePercent" => round((($AvailableRooms - $counter) / $AvailableRooms) * 100, 2),
                    ];
                }
            }
        }

        return array_values($dates);
    }
}
