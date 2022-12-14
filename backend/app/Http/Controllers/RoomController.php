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

    private function hideAttributes()
    {
        return ['resourceId', 'title', 'background', 'check_out_time', 'postings'];
    }

    private function getCustomersBreakfastOnly($food)
    {
        $onlyBreakfastAdult = [
            $food['Break_fast_price']['adult'],
            $food['Break_fast_with_dinner_price']['adult'],
            $food['Break_fast_with_lunch_price']['adult'],
            $food['full_board_price']['adult'],
        ];

        $onlyBreakfastChild = [
            $food['Break_fast_price']['child'],
            $food['Break_fast_with_dinner_price']['child'],
            $food['Break_fast_with_lunch_price']['child'],
            $food['full_board_price']['child'],
        ];
        $onlyBreakfastBaby = [
            $food['Break_fast_price']['baby'],
            $food['Break_fast_with_dinner_price']['baby'],
            $food['Break_fast_with_lunch_price']['baby'],
            $food['full_board_price']['baby'],
        ];

        return  [
            'adult' => array_sum($onlyBreakfastAdult),
            'child' => array_sum($onlyBreakfastChild),
            'baby' => array_sum($onlyBreakfastBaby),
        ];
    }

    private function getCustomersLunchOnly($food)
    {
        $onlyLunchAdult = [
            $food['Break_fast_with_lunch_price']['adult'],
            $food['full_board_price']['adult'],
        ];

        $onlyLunchChild = [
            $food['Break_fast_with_lunch_price']['child'],
            $food['full_board_price']['child'],
        ];
        $onlyLunchBaby = [
            $food['Break_fast_with_lunch_price']['baby'],
            $food['full_board_price']['baby'],
        ];

        return  [
            'adult' => array_sum($onlyLunchAdult),
            'child' => array_sum($onlyLunchChild),
            'baby' => array_sum($onlyLunchBaby),
        ];
    }

    private function getCustomersDinnerOnly($food)
    {
        $onlyDinnerAdult = [
            $food['Break_fast_with_dinner_price']['adult'],
            $food['full_board_price']['adult'],
        ];

        $onlyDinnerChild = [
            $food['Break_fast_with_dinner_price']['child'],
            $food['full_board_price']['child'],
        ];
        $onlyDinnerBaby = [
            $food['Break_fast_with_dinner_price']['baby'],
            $food['full_board_price']['baby'],
        ];

        return  [
            'adult' => array_sum($onlyDinnerAdult),
            'child' => array_sum($onlyDinnerChild),
            'baby' => array_sum($onlyDinnerBaby),
        ];
    }


    private function getFoodForCustomers($foodForMembers)
    {
        $room_only_price = $foodForMembers->clone()->where('meal', 'room_only_price');
        $Break_fast_price = $foodForMembers->clone()->where('meal', 'Break_fast_price');
        $Break_fast_with_dinner_price = $foodForMembers->clone()->where('meal', 'Break_fast_with_dinner_price');
        $Break_fast_with_lunch_price = $foodForMembers->clone()->where('meal', 'Break_fast_with_lunch_price');
        $full_board_price = $foodForMembers->clone()->where('meal', 'full_board_price');

        $food =   [
            'room_only_price' => [
                'adult' => $room_only_price->sum('no_of_adult'),
                'child' => $room_only_price->sum('no_of_child'),
                'baby' => $room_only_price->sum('no_of_baby'),
            ],
            'Break_fast_price' => [
                'adult' => $Break_fast_price->sum('no_of_adult'),
                'child' => $Break_fast_price->sum('no_of_child'),
                'baby' => $Break_fast_price->sum('no_of_baby'),
            ],
            'Break_fast_with_dinner_price' => [
                'adult' => $Break_fast_with_dinner_price->sum('no_of_adult'),
                'child' => $Break_fast_with_dinner_price->sum('no_of_child'),
                'baby' => $Break_fast_with_dinner_price->sum('no_of_baby'),
            ],
            'Break_fast_with_lunch_price' => [
                'adult' => $Break_fast_with_lunch_price->sum('no_of_adult'),
                'child' => $Break_fast_with_lunch_price->sum('no_of_child'),
                'baby' => $Break_fast_with_lunch_price->sum('no_of_baby'),
            ],
            'full_board_price' => [
                'adult' => $full_board_price->sum('no_of_adult'),
                'child' => $full_board_price->sum('no_of_child'),
                'baby' => $full_board_price->sum('no_of_baby'),
            ],
        ];

        return [
            'onlyBreakfast' => $this->getCustomersBreakfastOnly($food),
            'onlyLunch' => $this->getCustomersLunchOnly($food),
            'onlyDinner' => $this->getCustomersDinnerOnly($food),
        ];
    }

    public function roomListForGridView(Request $request)
    {
        $company_id = $request->company_id;
        $foodForMembers = BookedRoom::select('id', 'booking_id', 'no_of_adult', 'no_of_child', 'no_of_baby')
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', 2);
                $q->where('company_id', $company_id);
            })->withOut('booking');

        $fooForCustomers =   $this->getFoodForCustomers($foodForMembers);

        // ======================

        $dirtyRooms = BookedRoom::whereHas('booking', function ($q)  use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 3);
            $q->where('company_id', $company_id);
        })->count();

        $expectCheckInModel = BookedRoom::query();
        $expectCheckIn      = $expectCheckInModel->whereDate('check_in', $request->check_in)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 1);
                $q->where('company_id', $company_id);
            })->get();

        $expectCheckOutModel = BookedRoom::query();
        $expectCheckOut      = $expectCheckOutModel->clone()->whereDate('check_out', $request->check_in)
            ->whereHas('booking', function ($q)  use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();


        $checkInModel = BookedRoom::query();
        $checkIn      = $checkInModel->clone()->whereDate('check_in', $request->check_in)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();


        $checkOutModel = BookedRoom::query();
        $checkOut      = $checkOutModel->clone()->whereDate('check_out', $request->check_in)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 3);
                $q->where('company_id', $company_id);
            })->get();

        $confirmedBooking = BookedRoom::whereHas('booking', function ($q)  use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '!=', 0);
            $q->where('company_id', $company_id);
        })->count();

        $waitingBooking = BookedRoom::whereHas('booking', function ($q)  use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '=', 0);
            $q->where('company_id', $company_id);
        })->count();

        $checkInRooms = BookedRoom::whereHas('booking', function ($q)  use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('company_id', $company_id);
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
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
                $q->where('company_id', $company_id);
            })
            ->with('booking')
            ->pluck('room_id');

        $notAvailableRooms = Room::whereIn('id', $roomIds)
            ->where('company_id', $company_id)

            ->with('bookedRoom', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
                $q->where('company_id', $company_id);
            })->get();

        // return Room::whereIn('id', $roomIds)->with('bookedRoom.booking:id')->get();

        return [
            'dirtyRooms'    => $dirtyRooms,

            'notAvailableRooms' => $notAvailableRooms,
            // 'notAvailableRooms' => Room::whereIn('id', $roomIds)->with('bookedRoom.booking')->get(), //$notAvailableRooms,
            'availableRooms'    => Room::whereNotIn('id', $roomIds)->where('company_id', $company_id)->get(),
            'confirmedBooking'  => $confirmedBooking,
            'waitingBooking'    => $waitingBooking,
            'expectCheckIn'     => $expectCheckIn,
            'expectCheckOut'    => $expectCheckOut,

            'checkIn'    => $checkIn,
            'checkOut'    => $checkOut,

            'members'           => [
                'adult' => array_sum($no_of_adult),
                'child' => array_sum($no_of_child),
                'baby'  => array_sum($no_of_baby),
            ],

            'fooForCustomers' => $fooForCustomers,
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
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
            })
            ->pluck('room_id');
        return Room::whereNotIn('id', $roomIds)
            ->where('company_id', $request->company_id)
            ->get();
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

    public function get_room_price_by_meal_plan(Request $request)
    {
        return RoomType::where('company_id', $request->company_id)
            ->where('name', $request->room_type)
            ->pluck($request->slug)[0];
    }

    public function getFoodPrices(Request $request)
    {
        return  DB::table('food_prices')
            ->where('company_id', $request->company_id)
            ->get();
    }
}
