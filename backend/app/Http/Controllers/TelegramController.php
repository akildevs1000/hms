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
        // Get the incoming update
        $update = $request->all();

        // Check if the update contains a message
        if (isset($update['message'])) {
            $message = $update['message'];
            
            // Log the received message
            Log::info('Received message:', $message);

            // Example: handle text messages
            if (isset($message['text'])) {
                $text = $message['text'];
                // Process the text message as needed
                Log::info('Text message:', $text);
            }

            // Handle other types of messages if needed
            // Example: Handle photo messages
            if (isset($message['photo'])) {
                Log::info('Received photo message:', $message['photo']);
            }

            // Add more handlers for other message types if needed
        } else {
            // Optionally log or ignore non-message updates
            Log::info('Non-message update received:', $update);
        }

        // Respond to Telegram with 200 OK
        return response()->json(['status' => 'ok']);
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
