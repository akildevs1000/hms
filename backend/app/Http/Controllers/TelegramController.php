<?php

namespace App\Http\Controllers;

use App\Models\TelegramChatId;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    private $botToken = '7356807670:AAGtb_m3juvOpUGZCBaMXK73oO7A0-iUPOg';

    public function generateOtp(Request $request, $id)
    {
        try {
            $otp = $request->otp;
            $user = User::find($id);
            $user->telegram_otp = $request->otp;
            $user->telegram_otp_expires_at = Carbon::now()->addMinutes($request->expire_in_min ?? 1); // Expires in 1 minutes
            $user->save();
            return response()->json(['message' => 'OTP sent', 'otp' => $otp]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'OTP is invalid or expired'], 400);
        }
    }

    public function validateOtp(Request $request, $id)
    {
        // Find the user by user ID or Telegram chat ID
        $user = User::find($id);

        unset($user["assigned_permissions"]);

        // Check if the OTP is correct and not expired
        if ($user && $user->telegram_otp == $request->otp && Carbon::now()->lessThan($user->telegram_otp_expires_at)) {
            $user->telegram_otp = null;
            $user->telegram_otp_expires_at = null;
            $user->save();
            return response()->json(['message' => 'OTP is valid']);
        } else {
            // OTP is invalid or expired
            return response()->json(['message' => 'OTP is invalid or expired'], 400);
        }
    }

    public function updateTelegramChatId(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $user->telegram_chat_id = $request->telegram_chat_id;
            $user->save();
            return response()->json(['message' => 'Telegram chat id has been updated']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'OTP is invalid or expired'], 400);
        }
    }

    public function webhook(Request $request)
    {
        Log::info("Francis " . date("d M y H:s:i"));
        // // Retrieve the incoming message from Telegram
        // $message = $request->input('message');

        // if ($message) {
        //     $chatId = $message['chat']['id'];
        //     $text = strtolower($message['text']);

        //     if ($text === '1111') {
        //         // Send a response back to the user using Telegram API
        //         $this->sendMessage($chatId, 'Hello! How can I help you?');
        //     }
        // }

        return response()->json(['status' => 'success'], 200);
    }

    public function sendMessage($chatId, $message)
    {
        // Use Guzzle to send a message via the Telegram API
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $response->json();
    }


    public function storeNewChatId()
    {
        TelegramChatId::create([
            "chat_id" => request("chat_id", 0),
            "company_id" => request("company_id", 0)
        ]);
    }
}
