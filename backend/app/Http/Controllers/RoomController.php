<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Room;
use App\Models\RoomType;
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
        } catch (\Throwable$th) {
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
        $arr  = [];
        $data = Room::with('roomType')->get();
        foreach ($data as $d) {
            // $color =  $this->get_color($d->roomType->name);
            $arr[] = [
                'id'               => $d->room_no,
                'room_no'          => $d->room_no,
                'room_type'        => $d->roomType->name,
                // 'eventColor' => $color,
                'eventBorderColor' => 'white',
                'status'           => $d->status,
            ];
        }

        return $arr;
    }

    public function roomListForMenu()
    {
        return Room::with('roomType')->get();
    }

    public function roomListForGridView(Request $request)
    {

        $expectCheckInModel = BookedRoom::query();
        $expectCheckIn      = $expectCheckInModel->whereDate('check_in', $request->check_in)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 1);
            })->get();

        $expectCheckOutModel = BookedRoom::query();
        $expectCheckOut      = $expectCheckOutModel->clone()->whereDate('check_out', $request->check_in)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
            })->get();

        $confirmedBooking = BookedRoom::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '!=', 0);
        })->count();

        $waitingBooking = BookedRoom::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '=', 0);
        })->count();

        $checkInRooms = BookedRoom::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 2);
        })->get(["booking_id", "no_of_adult", "no_of_child", "no_of_baby"])->toArray();
        [
            $no_of_adult = array_column($checkInRooms, 'no_of_adult'),
            $no_of_child = array_column($checkInRooms, 'no_of_child'),
            $no_of_baby = array_column($checkInRooms, 'no_of_baby'),
        ];

        $model   = BookedRoom::query();
        $roomIds = $model
            ->whereDate('check_in', '<=', $request->check_in)
            // ->WhereDate('check_out', '>=', $request->check_out)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
            })
            ->with('booking')
            // ->get();
            ->pluck('room_id');

        $notAvailableRooms = Room::whereIn('id', $roomIds)
            ->with('bookedRoom', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
            })->get();

        // return Room::whereIn('id', $roomIds)->with('bookedRoom.booking:id')->get();

        return [
            'notAvailableRooms' => $notAvailableRooms,
            // 'notAvailableRooms' => Room::whereIn('id', $roomIds)->with('bookedRoom.booking')->get(), //$notAvailableRooms,
            'availableRooms'    => Room::whereNotIn('id', $roomIds)->get(),
            'confirmedBooking'  => $confirmedBooking,
            'waitingBooking'    => $waitingBooking,
            'expectCheckIn'     => $expectCheckIn,
            'expectCheckOut'    => $expectCheckOut,

            'members'           => [
                'adult' => array_sum($no_of_adult),
                'child' => array_sum($no_of_child),
                'baby'  => array_sum($no_of_baby),
            ],
        ];

        $arr  = [];
        $data = Room::with('roomType')->orderBy('status', 'desc')->get();
        foreach ($data as $d) {
            $color = $this->get_color($d->roomType->name);
            $arr[] = [
                'id'         => $d->id,
                'room_no'    => $d->room_no,
                'room_type'  => $d->roomType->name,
                'eventColor' => $color,
                'status'     => $d->status,
            ];
        }
        return $arr;
    }

    public function getAvailableRoomsByDate(Request $request)
    {
        $model   = BookedRoom::query();
        $roomIds = $model
            ->whereDate('check_in', '<=', $request->check_in)
            ->WhereDate('check_out', '>=', $request->check_out)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', 0);
            })
            // ->get();
            ->pluck('room_id');
        return Room::whereNotIn('id', $roomIds)
            ->get();
    }

    public function get_color($val)
    {
        return match($val) {
            'queen' => 'red',
            'king' => 'green',
            'castle' => '#9966CC',
            'royal' => '#000',
        };
    }

    public function get_room_price_by_meal_plan(Request $request)
    {
        return RoomType::where('company_id', $request->company_id)
            ->where('name', $request->room_type)
            ->pluck($request->slug)[0];
    }
}
