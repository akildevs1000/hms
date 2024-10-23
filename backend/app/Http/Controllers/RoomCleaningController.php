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
    public function index()
    {
        $query = RoomCleaning::query();

        $query->where('company_id', request('company_id', 0));

        if (request()->has('from_date') && request()->has('to_date')) {
            $query->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }

        if (request()->has('date')) {
            $query->whereDate('created_at', request('date'));
        }

        $query->whereIn('room_id', request('room_ids', []));


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

        $found = RoomCleaning::whereDate("created_at", date("Y-m-d"))
            ->where("room_id", $validatedData["room_id"])
            ->first();

        if (request("can_change_status") && $validatedData["status"] == RoomCleaning::CLEANED) {
            BookedRoom::where('room_id', $validatedData["room_id"])
                ->update(['booking_status' => BookedRoom::AVAILABLE]);
        }

        if ($found) {
            $found->update($validatedData);
            return $found;
        }

        return RoomCleaning::create($validatedData);
    }

    public function getNewEvent($company)
    {
        return RoomCleaning::where('company_id', $company)->whereDate("created_at", date("Y-m-d"))->count();
    }
}
