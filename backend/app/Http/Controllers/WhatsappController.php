<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

    public function sentOTPNew($user)
    {
        // return $user->mobile == '918220312148';
        try {

            $number = env('COUNTRY_CODE') . $user->mobile;
            $token = env('WHATSAPP_ACCESS_TOKEN');
            $instance_id = $user->company->whatsapp_instance_id;

            $msg = $this->processMessage($user);

            $url = "https://ezwhat.com/api/send.php?number={$number}&type=text&message={$msg}&instance_id={$instance_id}&access_token={$token}";

            $response = Http::withoutVerifying()->get($url);

            $response->status();
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

    // francis testing...
    public function sentNotificationTest(Request $request)
    {

        $url = "https://ezwhat.com/api/send.php?number={$request->number}&type=text&message={$request->message}&instance_id={$request->instance_id}&access_token={$request->access_token}";

        $response = Http::withoutVerifying()->get($url);

        return $response->status();
    }

    public function sentWhatsappOtp(Request $request)
    {


        try {
            $user = User::with('company')->find($request->userId);
            $user->otp = mt_rand(100000, 999999);


            if ($user->save()) {
                // if (app()->isProduction()) {
                $this->sentOTPNew($user);

                // }
                return $this->response('updated.', null, true);
            }
        } catch (\Throwable $th) {
            return $this->response($th, null, true);
        }
    }

    public function processMessage($user)
    {
        $company      = $user->company;
        $customerName = ucfirst($user['name']) ?? 'Guest';

        $msg          = "";
        $msg          .= "$company->company_code" ?? 0 . "\n";
        $msg          .= "\n";
        $msg          .= "Dear  $customerName, \n";

        $msg          .= "\n";
        $msg          .= "--------------- \n";
        $msg          .= "Your OTP  \n";
        $msg          .= "--------------- \n";
        $msg          .= "\n";
        $msg          .= "$user->otp \n";

        return $msg;
    }
}
