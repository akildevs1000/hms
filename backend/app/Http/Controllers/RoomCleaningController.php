<?php
namespace App\Http\Controllers;

use App\Http\Requests\RoomCleaning\StoreRequest;
use App\Models\BookedRoom;
use App\Models\RoomCleaning;
use Illuminate\Http\Request;

class RoomCleaningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function data()
    {
        $query = RoomCleaning::query();

        $query->where('company_id', request('company_id', 0));

        if (request()->has('from_date') && request()->has('to_date')) {
            $query->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }

        if (request()->has('date')) {
            $query->whereDate('created_at', request('date'));
        }

        if (request()->has('room_ids')) {
            $query->whereIn('room_id', request('room_ids'));
        }

        $query->orderBy("id", "desc");

        $query->with("room", "cleaned_by_user", "response_by_user");

        return $query->paginate(request("per_page", 1000));
    }

    public function index()
    {
        // Initial query to filter by company_id and dates
        $query = RoomCleaning::query()
            ->where('company_id', request('company_id', 0));

        if (request()->has('from_date') && request()->has('to_date')) {
            $query->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }

        if (request()->has('date')) {
            $query->whereDate('created_at', request('date'));
        }

        // Get the latest `id` for each `room_id` by selecting the max `created_at` for each room
        $latestRoomIds = $query->whereIn('room_id', request('room_ids', []))
            ->selectRaw('MAX(id) as id')
            ->groupBy('room_id');

        // Main query to fetch the full data of the latest cleaning records
        $query = RoomCleaning::whereIn('id', $latestRoomIds->pluck('id'))
            ->with("room", "cleaned_by_user", "response_by_user")
            ->orderBy("id", "desc");

        return $query->paginate(request("per_page", 1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        if (request()->has('attachments')) {
            $attachments     = request('attachments');      // array of base64 images
            $attachmentNames = request('attachment_names'); // array of names

            $publicDirectory = public_path("attachments");
            if (! file_exists($publicDirectory)) {
                mkdir($publicDirectory, 0755, true);
            }

            $savedFiles = [];

            foreach ($attachments as $index => $base64Image) {
                // Extract the base64 encoded data (strip the data:image/...;base64, prefix)
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $imageData   = base64_decode($base64Image);

                    if ($imageData === false) {
                        continue; // invalid base64, skip
                    }

                    $imageType = strtolower($type[1]); // jpeg, png, gif, etc.

                    // Create image resource from decoded data depending on type
                    switch ($imageType) {
                        case 'jpeg':
                        case 'jpg':
                            $image = imagecreatefromstring($imageData);
                            break;
                        case 'png':
                            $image = imagecreatefromstring($imageData);
                            break;
                        case 'gif':
                            $image = imagecreatefromstring($imageData);
                            break;
                        default:
                            // unsupported image type, skip
                            continue 2;
                    }

                    if (! $image) {
                        continue; // failed to create image
                    }

                    // Prepare filename with .png extension
                    $imageName = $attachmentNames[$index] ?? 'attachment_' . time() . '_' . $index . '.png';
                    if (pathinfo($imageName, PATHINFO_EXTENSION) !== 'png') {
                        $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '.png';
                    }

                    $filePath = $publicDirectory . DIRECTORY_SEPARATOR . $imageName;

                    // Save the image resource as PNG
                    imagepng($image, $filePath);
                    imagedestroy($image);

                    $savedFiles[] = $imageName;
                }
            }

            $validatedData['attachments'] = $savedFiles;
        }

        // return $validatedData;

        if (request()->has('voice_note')) {
            $base64VoiceNote = base64_decode(preg_replace('#^data:audio/\w+;base64,#i', '', request('voice_note')));
            $voiceNoteName   = request('voice_note_name');
            $publicDirectory = public_path("voice_notes");
            if (! file_exists($publicDirectory)) {
                mkdir($publicDirectory);
            }
            file_put_contents($publicDirectory . '/' . $voiceNoteName, $base64VoiceNote);

            $validatedData["voice_note"] = $voiceNoteName;
        }

        if (request("can_change_status") && $validatedData["status"] == RoomCleaning::CLEANED) {
            BookedRoom::where('room_id', $validatedData["room_id"])
                ->update(['booking_status' => BookedRoom::AVAILABLE]);
        }

        return RoomCleaning::create($validatedData);
    }

    public function getNewEvent($company)
    {
        return RoomCleaning::where('company_id', $company)->whereDate("created_at", date("Y-m-d"))->count();
    }

    public function uploadAttachment()
    {
        if (request()->has('attachment')) {
            $base64Image     = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('attachment')));
            $imageName       = request('attachment_name');
            $publicDirectory = public_path("after_attachments");
            if (! file_exists($publicDirectory)) {
                mkdir($publicDirectory);
            }
            file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

            return RoomCleaning::where("id", request('id'))->update(["after_attachment" => $imageName]);
        }
    }

    public function uploadVoiceNote()
    {
        if (request()->has('attachment')) {
            $base64Image     = base64_decode(preg_replace('#^data:audio/\w+;base64,#i', '', request('attachment')));
            $imageName       = request('attachment_name');
            $publicDirectory = public_path("maintenance_voice_notes");
            if (! file_exists($publicDirectory)) {
                mkdir($publicDirectory);
            }
            file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

            return RoomCleaning::where("id", request('id'))->update(["maintenance_voice_note" => $imageName]);
        }
    }
}
