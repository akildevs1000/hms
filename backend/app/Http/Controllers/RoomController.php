<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request, Room $model)
    {
        return $model->where('company_id', $request->company_id)->paginate($request->per_page);
    }

    public function search(Request $request, $key)
    {
        $model = Room::query();
        $fields = [
            'name',
            'contact_no',
            'email',
            "id_card_type_id",
            "id_card_no",
            'car_no',
            "no_of_adult",
            "no_of_child",
            "no_of_baby",
            "address",
        ];
        $model = $this->process_search($model, $key, $fields);
        return $model->where("company_id", $request->company_id)->paginate($request->perPage);
    }

    public function update($room_id, $status)
    {
        return Room::where('id', $room_id)->update(["status" => $status]);
    }
}
