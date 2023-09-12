<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsappController extends Controller
{
    public function sentNotification($data)
    {
        try {
            if ($data['instance_id']) {

                if ($data['whatsapp_access_token'] == '') {
                    $data['whatsapp_access_token'] = env('WHATSAPP_ACCESS_TOKEN');
                }

                $response = Http::withoutVerifying()->get(env('WHATSAPP_URL'), [
                    'number' => $data['to'],
                    'type' => 'text',
                    'message' => $data['message'],
                    'instance_id' => $data['instance_id'],
                    'access_token' => $data['whatsapp_access_token'], //env('WHATSAPP_ACCESS_TOKEN'),
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
                    'number' => $data['to'],
                    'type' => 'text',
                    'message' => $data['message'],
                    'instance_id' => env('OTP_INSTANCE_ID'),
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

    public function sentOTPNew($user)
    {
        // return $user->mobile == '918220312148';

        if ($user->enable_whatsapp_otp == 1) {
            try {

                $number = env('COUNTRY_CODE') . $user->mobile;
                $token = env('WHATSAPP_ACCESS_TOKEN');
                $instance_id = env('OTP_ID');

                $msg = $this->processMessage($user);

                $url = "https://ezwhat.com/api/send.php?number={$number}&type=text&message={$msg}&instance_id={$instance_id}&access_token={$token}";

                $response = Http::withoutVerifying()->get($url);

                $response->status();

                return ['message' => $response];
            } catch (\Throwable $th) {
                Log::channel("custom")->error("BookingController: " . $th);
            }
        } else {
        }
    }
    public function sentTwilio($user = null)
    {
        //composer require twilio/sdk

        $number = '+971582608568'; // '+' . env('COUNTRY_CODE') . $user->mobile;
        //$number = '+91' . $user->mobile;
        $msg = "Hello"; //$this->processMessage($user);
        //Twilio
        $sid = "ACd6488bdbbbdeaae210b94f2a3dc21d24";
        $token = "5d437943999440ccd6697a55d4ca21ab";
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
            ->create(
                "whatsapp:{$number}", // to
                array(
                    "from" => "whatsapp:+14155238886",
                    "body" => $msg,
                )
            );
        Log::channel('whatsapp_logs')->info($message);
        return ['message' => $message->sid, 'mobile_number' => $number, 'msg' => $msg];
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

    // francis testing...
    public function sentNotificationTest(Request $request)
    {

        $url = "https://ezwhat.com/api/send.php?number={$request->number}&type=text&message={$request->message}&instance_id={$request->instance_id}&access_token={$request->access_token}";

        $response = Http::withoutVerifying()->get($url);

        return $response->status();
    }

    public function sentNotificationTest2()
    {

        $sid = "ACd6488bdbbbdeaae210b94f2a3dc21d24";
        $token = "5d437943999440ccd6697a55d4ca21ab";
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
            ->create(
                "whatsapp:+919701226007", // to
                array(
                    "from" => "whatsapp:+14155238886",
                    "body" => "Your appointment is coming up on July 21 at 3PM",
                )
            );

        print($message->sid);
        // $sid = config('ACd6488bdbbbdeaae210b94f2a3dc21d24');
        // $token = config('5d437943999440ccd6697a55d4ca21ab');
        // $client = new Client($sid, $token);

        // // Replace the following with the actual phone number and Twilio purchased number
        // $phoneNumber = "+919701226007";
        // $twilioPurchasedNumber = "+XXXXXXXXXX";

        // // Send a text message
        // $message = $client->messages->create(
        //     $phoneNumber,
        //     [
        //         'from' => $twilioPurchasedNumber,
        //         'body' => "Hey Jenny! Good luck on the bar exam!",
        //     ]
        // );
        // $messageSid = $message->sid;

        // return "Message sent successfully with SID: $messageSid";
    }
    public function getMessages()
    {
        require __DIR__ . '/../src/Twilio/autoload.php';

        $sid = config('services.twilio.account_sid');
        $token = config('services.twilio.auth_token');
        $client = new Client($sid, $token);

        // Print the last 10 messages
        $messageList = $client->messages->read([], 10);
        $messages = [];

        foreach ($messageList as $msg) {
            $messages[] = [
                'ID' => $msg->sid,
                'From' => $msg->from,
                'To' => $msg->to,
                'Status' => $msg->status,
                'Body' => $msg->body,
            ];
        }

        return $messages;
    }

    public function sentWhatsappOtp(Request $request)
    {

        //try {
        $user = User::with('company')->find($request->userId);

        $user->otp = mt_rand(100000, 999999);

        if ($user->save()) {
            // if (app()->isProduction()) {
            $return = $this->sentOTPNew($user);

            // }
            return $this->response('updated.', $return, true);
        }
        // } catch (\Throwable $th) {
        //     return $this->response($th, null, true);
        // }
    }

    public function processMessage($user)
    {
        $company = $user->company;
        $customerName = ucfirst($user['name']) ?? 'Guest';

        $msg = "";
        $msg .= "$company->company_code" ?? 0 . "\n";
        $msg .= "\n";
        $msg .= "Dear  $customerName, \n";

        $msg .= "\n";
        $msg .= "--------------- \n";
        $msg .= "Your OTP  \n";
        $msg .= "--------------- \n";
        $msg .= "\n";
        $msg .= "$user->otp \n";

        return $msg;
    }
}
