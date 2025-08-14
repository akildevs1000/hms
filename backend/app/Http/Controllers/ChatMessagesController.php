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
            "booking_room_id" => "required",
            "company_id" => "required",


            "room_id" => "required",
            "room_number" => "required",
            "ts" => "required",
            "text" => "required",
            "type" => "required",
            "filename" => "nullable",
            "receiption_name" => "nullable",

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

    public function getChatUploadFile(Request $request)
    {
        $validate = [
            "role" => "required",
            "sender" => "required",
            "booking_id" => "required",
            "booking_room_id" => "required",
            "company_id" => "required",
            "room_id" => "required",
            "room_number" => "required",
            "ts" => "required",
            "text" => "nullable",
            "type" => "required",
            "filename" => "nullable",
            "receiption_name" => "nullable",

        ];

        $data = $request->validate($validate);



        $data["ts"] = date("Y-m-d H:i:s", $data["ts"] / 1000);

        $response =  ChatMessages::create($data);











        if ($response) {


            if ($request->hasFile('file')) {

                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();
                $fileName = $response->id    . "." . $ext;


                $folder = 'hotel/chat/' . $request->company_id . "/" . $request->booking_room_id;
                $destinationPath = public_path($folder);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $fileName);

                ChatMessages::where("id", $response->id)->update(["filename" => $fileName]);

                $url =  asset($folder . "/" . $fileName);
            }
            return $this->response($url, null, true);
        } else

            return $this->response("Failed", null, false);
    }

    public function getChatHistory(Request $request)
    {

        return  $model = ChatMessages::where("booking_room_id", $request->booking_room_id)
            ->orderBy("ts", "asc")->get();;
    }

    public function getChatBookingsList(Request $request)
    {
        $model = BookedRoom::where("company_id", $request->company_id);


        if ($request->filled('filterSearch') && $request->filterSearch !== '') {
            $wildCard = env('WILD_CARD', 'ILIKE');
            $search   = '%' . $request->filterSearch . '%';

            $model->where(function ($query) use ($search, $wildCard) {
                $query->orWhere('room_no', $wildCard, $search);
                // ->orWhereHas('customer', function ($q) use ($search, $wildCard) {
                //     $q->orWhere('first_name', $wildCard, $search)
                //         ->orWhere('last_name', $wildCard, $search);
                // });
            });
        }



        // ->where("check_in", "!=", null)
        $model->orderBy("check_in", "desc")->orderBy("room_no", "asc");;


        return $model->paginate($request->per_page ?? 25);
    }
}
