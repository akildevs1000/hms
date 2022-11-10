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

        if($request->status != "-1") {
            $model->where('status', $request->status);
        }
        return $model->where('company_id', $request->company_id)->orderByDesc("id")->paginate($request->per_page);
    }

    public function search(Request $request, $key)
    {

        $model = Room::query();
        $fields = [
            'room_no',
            'room_type_id',
        ];
        $model = $this->process_search($model, $key, $fields);
        return $model->where("company_id", $request->company_id)->where("status",$request->status)->paginate($request->per_page);
    }

    public function filter(Request $request)
    {
        $model = Room::query();
        return $model->where("company_id", $request->company_id)->where("status",$request->status)->paginate($request->per_page);
    }

    public function update($room_id, $status)
    {
        return Room::where('id', $room_id)->update(["status" => $status]);
    }

    public function getRoom($id)
    {
        return Room::where('room_type_id', $id)->get(['id', 'room_no']);
    }

    public function get_id_cards()
    {
        return DB::table('id_card_types')->get(['id', 'name']);
    }
}