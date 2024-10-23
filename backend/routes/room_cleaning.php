<?php

use App\Http\Controllers\RoomCleaningController;
use Illuminate\Support\Facades\Route;

Route::apiResource('room-cleaning', RoomCleaningController::class);
Route::get('room-cleaning-event/{id}', [RoomCleaningController::class, "getNewEvent"]);
Route::get('room-data', [RoomCleaningController::class, "data"]);


// {
//     "room_id": 101,
//     "status": "completed",
//     "start_time": "08:00",
//     "end_time": "10:30",
//     "total_time": "02:30",
//     "before_attachment": "https://example.com/attachments/before_cleaning.jpg",
//     "after_attachment": "https://example.com/attachments/after_cleaning.jpg",
//     "voice_note": "https://example.com/voice_notes/cleaning_voice_note.mp3",
//     "cleaned_by_user_id": 25,
//     "response_by_user_id": 30,
//     "company_id": 5
// }