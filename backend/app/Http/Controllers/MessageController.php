<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\Message\StoreRequest;
use App\Models\ChatPhoto;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        // event(new MessageSent("hello world"));
        // return;

        $sender_id = request("sender_id", 7); // Get authenticated user ID
        $receiverId = request("receiver_id", 4); // Get authenticated user ID

        return Message::with('sender', 'receiver', "chat_photos")
            ->where(function ($query) use ($sender_id, $receiverId) {
                $query->where('sender_id', $sender_id)
                    ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($sender_id, $receiverId) {
                $query->where('sender_id', $receiverId)
                    ->where('receiver_id', $sender_id);
            })
            ->get();
    }

    public function getLattestThreeMessages()
    {
        $companyId = request('company_id', 0);

        $cacheKey = "latest_three_messages_company_{$companyId}";

        return Cache::rememberForever($cacheKey, function () use ($companyId) {
            return Message::with('lattest_room', 'sender', 'receiver', 'chat_photos')
                ->where('company_id', $companyId)
                ->whereHas("lattest_room")
                ->latest() // Sort by created_at in descending order
                ->take(3) // Limit to the latest 3 messages
                ->get();
        });
    }

    public function store(StoreRequest $request)
    {
        // Validate incoming request data
        $validatedData = $request->validated();

        if (!request()->has("is_reponse")) {
            $validatedData["receiver_id"] = User::where("company_id", request("company_id", 0))->orderBy("id", "asc")->value("id");
        }
        $chat_photos = $request->input('chat_photos', []);

        $payload = [];

        // Start a transaction
        DB::beginTransaction();

        try {
            // Create the message

            if (request()->has('voice_note')) {
                $base64VoiceNote  = base64_decode(preg_replace('#^data:audio/\w+;base64,#i', '', request('voice_note')));
                $voiceNoteName  = request('voice_note_name');
                $publicDirectory = public_path("voice_notes");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $voiceNoteName, $base64VoiceNote);

                $validatedData["voice_note"] = $voiceNoteName;
            }

            $message = Message::create($validatedData);

            // Check if there are any chat photos
            if (!empty($chat_photos)) {
                foreach ($chat_photos as $chat_photo) {
                    // Decode the base64 image
                    $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $chat_photo));

                    // Generate a unique name for the image
                    $imageName = uniqid() . '.png'; // You could change the extension based on the content type
                    $destinationPath = public_path('chat_photos');

                    // Ensure the directory exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true); // Use recursive mkdir
                    }

                    // Store the file
                    $filePath = $destinationPath . '/' . $imageName;
                    file_put_contents($filePath, $base64Image);

                    // Prepare the payload for batch insert
                    $payload[] = [
                        'message_id' => $message->id, // Use the message ID
                        'photo_path' => $imageName, // Save the image name
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insert the chat photos in a single database call
                ChatPhoto::insert($payload);
            }
            (new Controller)->sendSignal($validatedData["company_id"]);

            // Commit the transaction
            DB::commit();

            // Optionally broadcast the message
            // broadcast(new \App\Events\MessageSent($message))->toOthers();

            return response()->json($message, 201); // Return created message with associated photos
        } catch (\Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to store message and photos: ' . $e->getMessage()], 500);
        }
    }

    public function updateChatStatus(Request $request)
    {
        try {
            Message::where("id", $request->id)->update(["status" => $request->status]);
            return response()->json("Record Update");
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function chatByCustomerId($id)
    {
        return Message::with(['lattest_room', 'sender', 'receiver', 'chat_photos'])
            ->where(function ($query) use ($id) {
                $query->where('sender_id', $id)
                    ->orWhere('receiver_id', $id);
            })
            ->get();
    }

    public function getLatestMessageCount(Request $request)
    {
        return Message::where('company_id', $request->company_id ?? 0)->count();
    }

    public function getLatestMessageCountByCustomerId($id)
    {
        return Message::where(function ($query) use ($id) {
            $query->where('sender_id', $id)
                ->orWhere('receiver_id', $id);
        })->count();
    }
}
