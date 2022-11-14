<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $model = Room::query();

        if ($request->status != "-1") {
            $model->where('status', $request->status);
        }
        return $model->where('company_id', $request->company_id)->orderByDesc("id")->paginate($request->per_page);
    }

    public function search(Request $request, $key)
    {

        $model  = Room::query();
        $fields = [
            'room_no',
            'room_type_id',
        ];
        $model = $this->process_search($model, $key, $fields);
        return $model->where("company_id", $request->company_id)->where("status", $request->status)->paginate($request->per_page);
    }

    public function filter(Request $request)
    {
        $model = Room::query();
        return $model->where("company_id", $request->company_id)->where("status", $request->status)->paginate($request->per_page);
    }

    public function update($room_id, $status)
    {
        try {

            $record = Room::where('id', $room_id)->update(["status" => $status]);

            if ($record) {
                return $this->response('Room successfully added.', $record, true);
            } else {
                return $this->response('Room cannot add.', null, 'Database error');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getRoom($id)
    {
        return Room::where('status', 0)->where('room_type_id', $id)->get(['id', 'room_no']);
    }

    public function get_id_cards()
    {
        return DB::table('id_card_types')->get(['id', 'name']);
    }

    public function roomList()
    {
        $arr = [];
        $data =    Room::with('roomType')->get();
        foreach ($data as   $d) {
            $color =  $this->get_color($d->roomType->name);
            $arr[] = [
                'id' => $d->room_no,
                'room_no' => $d->room_no,
                'room_type' => $d->roomType->name,
                'eventColor' => $color,
                'status' => $d->status,
            ];
        }

        return $arr;
    }

    public function roomListForGridView()
    {
        $arr = [];
        $data =    Room::with('roomType')->orderBy('status', 'desc')->get();
        foreach ($data as   $d) {
            $color =  $this->get_color($d->roomType->name);
            $arr[] = [
                'id' => $d->id,
                'room_no' => $d->room_no,
                'room_type' => $d->roomType->name,
                'eventColor' => $color,
                'status' => $d->status,
            ];
        }

        return $arr;
    }


    public function get_color($val)
    {
        return match ($val) {
            'queen' => 'red',
            'king' => 'green',
            'castle' => '#9966CC',
            'royal' => '#000',
        };
    }
}