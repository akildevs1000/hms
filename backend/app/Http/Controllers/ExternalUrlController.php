<?php

namespace App\Http\Controllers;

use App\Mail\ActionMarkdownMail;
use App\Mail\TestMail;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Room;
use App\Models\WhatsappClient;
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
        $url = 'https://wa.mytime2cloud.com/send-message';

        $accounts = WhatsappClient::where("company_id", request("company_id", 13))
            ->value("accounts");

        $clientId = !empty($accounts) ? last($accounts)["clientId"] ?? 0 : 0;

        // Data to send in the request
        $data = [
            'recipient' =>  request("number"),
            'text' => request("message"),
            'clientId' => $clientId,
        ];

        // Sending POST request using Http facade
        $response = Http::post($url, $data);

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


    public function sendMessageOLD()
    {
        // API endpoint URL
        $url = 'https://backend.mytime2cloud.com/api/send-whatsapp-wessage';

        // Data to send in the request
        $data = [
            'company_id' => 13,
            'mobile_number' =>  request("number"),
            'message' => request("message"),

        ];

        // Sending POST request using Http facade
        $response = Http::post($url, $data);

        // try {
        //     if (request("email"))
        //         Mail::to(request("email"))
        //             ->bcc("venuakil2@gmail.com")
        //             ->send(new TestMail(request("message"), request("message") . ' <br/> Mobile Number:' . request("number")));
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }

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

    public function sendMessage2()
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

    public function sandBox($id, $inv = "")
    {

        $invNo = $inv == "" ? "0000" . $id : $inv;

        $booking = Booking::with(['orderRooms', 'customer', 'company' => ['user', 'contact'], 'transactions.paymentMode', 'bookedRooms'])
            ->find($id);

        return $orderRooms = $booking->orderRooms;
        $company = $booking->company;
        $transactions = $booking->transactions;
        $bookedRooms = $booking->bookedRooms;

        $first_check_in_time = $bookedRooms[0]["check_in_time"] ?? "00:00";
        $first_check_out_time = $bookedRooms[0]["check_out_time"] ?? "00:00";


        $roomTypes = array_unique(array_column($booking->bookedRooms->toArray(), 'room_type'));
        $paymentMode = $transactions->toArray();
        $paymentMode = end($paymentMode);

        // $amtLatter = $this->amountToText($transactions->sum('debit') ?? 0);
        $amtLatter = $this->amountToText($booking->total_price ?? 0);

        $numberOfCustomers = $booking->bookedRooms->sum(function ($room) {
            return $room->no_of_adult + $room->no_of_child + $room->no_of_baby;
        });

        $roomsDiscount = $booking->bookedRooms->sum(function ($room) {
            return $room->room_discount;
        });

        $is_old_bill = strtotime($booking->created_at) - strtotime(date('2023-08-31'));

        $bladeName = 'invoice.invoice_updated_with_tax';

        return view($bladeName, compact("first_check_in_time", "first_check_out_time", "invNo", "booking", "orderRooms", "company", "transactions", "amtLatter", "numberOfCustomers", "paymentMode", "roomsDiscount", "roomTypes"));
    }

    public function getLastWhatsappClientId($id)
    {

        $accounts = WhatsappClient::where("company_id", $id)
            ->value("accounts");

        $clientId = !empty($accounts) ? last($accounts)["clientId"] ?? 0 : 0;

        return [
            "clientId" => $clientId
        ];
    }
}
