<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\ChatMessages;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();

        $validate = [
            "role" => "required",
            "sender" => "required",
            "booking_id" => "required",
            "room_id" => "required",
            "room_number" => "required",
            "ts" => "required",
            "text" => "required",
            "type" => "required",
            "filename" => "nullable",
            "receiption_name" => "required",

        ];

        $data = $request->validate($validate);



        $data["ts"] = date("Y-m-d H:i:s", $data["ts"] / 1000);

        $response =        ChatMessages::create($data);
        if ($response) {
            return $this->response(true, null, "Success");
        } else

            return $this->response(false, null, "Success");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChatMessages  $chatMessages
     * @return \Illuminate\Http\Response
     */
    public function show(ChatMessages $chatMessages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChatMessages  $chatMessages
     * @return \Illuminate\Http\Response
     */
    public function edit(ChatMessages $chatMessages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatMessages  $chatMessages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatMessages $chatMessages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChatMessages  $chatMessages
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatMessages $chatMessages)
    {
        //
    }

    public function getChatHistory(Request $request)
    {

        return  $model = ChatMessages::where("booking_id", $request->bookingId)
            ->orderBy("ts", "asc")->get();;
    }

    public function getBookingsList(Request $request)
    {
        $model = BookedRoom::where("company_id", $request->company_id)


            ->where("check_in", "!=", null)
            ->orderBy("check_in", "desc");;


        return $model->paginate($request->per_page ?? 10);
    }
}
