<?php

namespace App\Http\Controllers;

//use App\Http\Requests\Allowance\StoreRequest;

use App\Http\Requests\Room\StoreRequest;
use App\Http\Requests\Room\UpdateRequest;
use App\Models\BookedRoom;
use App\Models\Food;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function dropDown()
    {
        return Room::whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))->get();
    }
    public function index(Request $request)
    {
        $model = Room::query()->with("device");

        $model->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")));

        if ($request->filled('status') && $request->status != "-1") {
            $model->where('status', $request->status);
        }

        $model = $model->where('company_id', $request->company_id);

        //datatable Filters
        if ($request->filled('room_no')) {
            $model->where('room_no', 'like', "$request->room_no%");
        }
        if ($request->filled('room_type')) {
            $model->where('room_type_id', $request->room_type);
        }
        if ($request->filled('floor_no')) {
            $model->where('floor_no', $request->floor_no);
        }
        if ($request->filled('status')) {
            $model->where('status', $request->status);
        }
        if ($request->filled('latest_status')) {
            $model->whereHas('device', function ($q) use ($request) {
                return  $q->where('latest_status',    $request->latest_status);
            });
        }



        //  else {
        //     $model->where('status', 0);
        // }

        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';
            if (strpos($request->sortBy, '.')) {

                $model->orderBy(RoomType::select('name')->whereColumn('room_types.id', 'rooms.room_type_id'), $sortDesc);
            } else {
                $model->orderBy($request->sortBy, $sortDesc);
            }
        } else {
            $model->orderBy('room_no', 'ASC');
        }

        return $model->paginate($request->per_page);
    }

    public function search(Request $request, $key)
    {

        $model = Room::query();
        $fields = [
            'room_no',
            'room_type_id',
        ];
        $model = $this->process_search($model, $key, $fields);
        return $model->where("company_id", $request->company_id)->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))->where("status", $request->status)->paginate($request->per_page);
    }

    public function filter(Request $request)
    {
        $model = Room::query();
        return $model->where("company_id", $request->company_id)
            ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->where("status", $request->status)->paginate($request->per_page);
    }

    // public function update($room_id, $status)
    // {
    //     try {

    //         $record = Room::where('id', $room_id)->update(["status" => $status]);

    //         if ($record) {
    //             return $this->response('Room successfully added.', $record, true);
    //         } else {
    //             return $this->response('Room cannot add.', null, 'Database error');
    //         }
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }

    public function update(UpdateRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isRoomExist = Room::where('room_no', $request->room_no)
                    ->where('company_id', $request->company_id)
                    ->first();

                if ($isRoomExist) {
                    if ($isRoomExist->id != $id) {
                        return $this->response($request->room_no . ' Room Details are already Exist', null, false);
                    }
                }
                $status = Room::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Room Details are updated succesfully', $status, true);
                } else {
                    return $this->response('Room Details are not Updated', $status, false);
                }
            } else {
                return $this->response('Error Occured', $data, false);
            }
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    public function getRoom($id)
    {
        return Room::where('status', 0)
            ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->where('room_type_id', $id)->get(['id', 'room_no']);
    }

    public function get_id_cards()
    {
        return DB::table('id_card_types')->get(['id', 'name']);
    }

    public function roomList(Request $request)
    {
        $arr = [];
        $data = Room::with('roomType')
            ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            // ->where('status', 0)
            ->whereCompanyId($request->company_id)->get();
        foreach ($data as $d) {
            // $color =  $this->get_color($d->roomType->name);
            $arr[] = [
                'id' => $d->room_no,
                'table_id' => $d->id,
                'room_no' => $d->room_no,
                'room_type' => $d->roomType->name,
                // 'eventColor' => $color,
                'eventBorderColor' => 'white',
                'status' => $d->status,
            ];
        }

        return $arr;
    }

    public function roomListForCalendarOnly(Request $request)
    {
        $arr = [];
        $data = Room::with('roomType')->whereHas("roomType")->whereCompanyId($request->company_id)->get();
        foreach ($data as $d) {

            $arr[] = [
                'id' => $d->room_no,
                'table_id' => $d->id,
                'room_no' => $d->room_no,
                'room_type' => $d->roomType->name ?? "",
                // 'eventColor' => $color,
                'eventBorderColor' => 'white',
                'status' => $d->status,
            ];
        }

        return $arr;
    }


    public function roomDropdownList(Request $request)
    {
        $arr = [];
        return  $data = Room::with('roomType')
            // ->where('status', 0)
            ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->whereCompanyId($request->company_id)->orderBy("room_no", "ASC")->get();


        return $arr;
    }
    public function roomListForMenu()
    {
        return Room::with('roomType')->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))->get();
    }

    private function getCustomersBreakfastOnly($breakfast)
    {
        $breakfast_adult_sum = 0;
        $breakfast_child_sum = 0;
        $breakfast_baby_sum = 0;

        foreach ($breakfast as $item) {
            $breakfast_adult_sum += $item['adult'];
            $breakfast_child_sum += $item['child'];
            $breakfast_baby_sum += $item['baby'];
        }
        return [
            'adult' => $breakfast_adult_sum,
            'child' => $breakfast_child_sum,
            'baby' => $breakfast_baby_sum,
        ];
    }

    private function getCustomersLunchOnly($lunch)
    {

        $lunch_adult_sum = 0;
        $lunch_child_sum = 0;
        $lunch_baby_sum = 0;

        foreach ($lunch as $item) {
            $lunch_adult_sum += $item['adult'];
            $lunch_child_sum += $item['child'];
            $lunch_baby_sum += $item['baby'];
        }

        return [
            'adult' => $lunch_adult_sum,
            'child' => $lunch_child_sum,
            'baby' => $lunch_baby_sum,
        ];
    }

    private function getCustomersDinnerOnly($dinner)
    {

        $dinner_adult_sum = 0;
        $dinner_child_sum = 0;
        $dinner_baby_sum = 0;

        foreach ($dinner as $item) {
            $dinner_adult_sum += $item['adult'];
            $dinner_child_sum += $item['child'];
            $dinner_baby_sum += $item['baby'];
        }

        return [
            'adult' => $dinner_adult_sum,
            'child' => $dinner_child_sum,
            'baby' => $dinner_baby_sum,
        ];
    }

    private function getFoodForCustomers($foodForMembers)
    {
        $room_only_price = $foodForMembers->clone()->where('meal', 'room_only_price');
        $Break_fast_price = $foodForMembers->clone()->where('meal', 'Break_fast_price');
        $Break_fast_with_dinner_price = $foodForMembers->clone()->where('meal', 'Break_fast_with_dinner_price');
        $Break_fast_with_lunch_price = $foodForMembers->clone()->where('meal', 'Break_fast_with_lunch_price');
        $full_board_price = $foodForMembers->clone()->where('meal', 'full_board_price');

        $food = [
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

        /////////$todayDate = date('Y-m-d');
        ////$todayDate = $request->check_in;
        // $diff_in_seconds = strtotime($request->check_in) - strtotime($todayDate);
        // if ($diff_in_seconds < 0) {
        //     return response()->json(['data' => 'Your system Date is wrong', 'status' => false]);
        // }

        if ($request->filled("filter_date")) {
            $todayDate = $request->filter_date;
        } else {
            $todayDate = date('Y-m-d');
        }

        $CheckedOut = Room::with('device')
            // ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->whereHas('bookedRoom', function ($query) use ($company_id, $todayDate) {
                $query->whereDate('check_out', date("Y-m-d"));
                $query->where('company_id', $company_id);
                $query->where('booking_status', 3);
            })
            ->with(['bookedRoom' => function ($q) use ($company_id) {
                $q->where("company_id", $company_id);
                $q->where('booking_status', 3);
                $q->with("customer");
            }])
            ->get();

        $expectCheckOut = Room::with('device')
            // ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->whereHas('bookedRoom', function ($query) use ($company_id, $todayDate) {
                $query->where('company_id', $company_id);
                $query->whereDate('check_out', $todayDate);
                $query->where("booking_status", ">", 0);
            })
            ->with(['bookedRoom' => function ($q) use ($company_id) {
                $q->where("company_id", $company_id);
                $q->with("customer");
                $q->where("booking_status", ">", 0);
            }])
            ->get();

        $expectCheckIn = Room::whereHas('bookedRoom', function ($q) use ($company_id, $todayDate) {
            $q->whereDate('check_in', '<=', $todayDate);
            $q->where('booking_status', BookedRoom::BOOKED);
            $q->where('company_id', $company_id);
            $q->whereHas('booking', function ($q) use ($company_id) {
                $q->where('advance_price', ">", 0);
                $q->where('company_id', $company_id);
            });
            $q->whereNotNull('room_id');
            $q->where('company_id', $company_id);
        })
            ->with('device')
            ->with(['bookedRoom' => function ($q) use ($company_id) {
                $q->where("company_id", $company_id);
                $q->where("booking_status", ">", 0);
                $q->with("customer");
            }])

            ->get();

        $reservedWithoutAdvance = Room::whereHas('bookedRoom', function ($q) use ($company_id, $todayDate) {
            $q->whereDate('check_in', $todayDate);
            $q->where('booking_status', BookedRoom::BOOKED);
            $q->whereNotNull('room_id');
            $q->where('company_id', $company_id);
        })
            ->with(['device', 'bookedRoom' => function ($q) use ($company_id, $todayDate) {
                $q->whereDate('check_in', $todayDate);
                $q->where("company_id", $company_id);
                $q->where("booking_status", ">", 0);
                $q->with("customer");
            }])
            ->get();

        $AvailableRooms = Room::with('device')
            ->where('company_id', $company_id)
            ->where('status', '!=', Room::Blocked)
            ->whereDoesntHave('bookedRoom', function ($q) use ($company_id, $todayDate) {
                $q->where('company_id', $company_id);
                $q->whereDate('check_out', $todayDate);
                $q->where("booking_status", 0);

                // // Option 1: Check for rooms that are not booked for the given date range
                // $q->where(function ($query) use ($todayDate) {
                //     $query
                //         ->whereDate("check_in", ">=", $todayDate)
                //         ->whereDate("check_out", ">=", $todayDate)
                //         ->where("booking_status", 0);
                // });
                // // Option 2: Exclude rooms based on booking status
                // ->orWhere(function ($query) use ($todayDate) {
                //     $query->whereDate("check_out", date('Y-m-d', strtotime($todayDate . " +1 day")));
                //     $query->where("booking_status",">",0);
                //     // $query->whereDate("check_out", "<", date('Y-m-d', strtotime($todayDate . " +1 day")));
                // });
            })
            ->get();


        $BlockedRooms = Room::with('device')->where("status", Room::Blocked)->where('company_id', $company_id)->get();

        $fooForCustomers = Food::whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 2);
            $q->where('company_id', $company_id);
        })->get(['breakfast', 'lunch', 'dinner'])->toArray();

        $breakfast = array_column($fooForCustomers, 'breakfast');
        $lunch = array_column($fooForCustomers, 'lunch');
        $dinner = array_column($fooForCustomers, 'dinner');

        $fooForCustomers = [
            'breakfast' => $this->getCustomersBreakfastOnly($breakfast),
            'lunch' => $this->getCustomersLunchOnly($lunch),
            'dinner' => $this->getCustomersDinnerOnly($dinner),
        ];

        $checkIn = Room::with('device')
            // ->whereHas('roomType', fn ($q) => $q->where('type', request("type", "room")))
            ->whereHas('bookedRoom', function ($query) use ($company_id, $todayDate) {
                $query->whereDate('check_in', $todayDate);
                $query->where('company_id', $company_id);
                $query->where('booking_status', 2);
            })
            ->with(['bookedRoom' => function ($q) use ($company_id, $todayDate) {
                $q->where("company_id", $company_id);
                $q->whereDate('check_in', $todayDate);
                $q->where("booking_status", ">", 0);
                $q->with("customer");
            }])
            ->get();

        $checkOutModel = BookedRoom::with('device');
        $checkOut = $checkOutModel->clone()->whereDate('check_out', $todayDate)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->whereIn('booking_status', [0, 3, 4, 5]);
                // $q->where('booking_status', '>=', 3);
                // $q->whereDate('check_in', '<=', $request->check_in);
                $q->where('company_id', $company_id);
            })->get();

        $confirmedBooking = BookedRoom::with('device')->whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '!=', 0);
            $q->where('company_id', $company_id);
        });

        $waitingBooking = BookedRoom::with('device')->whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '=', 0);
            $q->where('company_id', $company_id);
        })->count();

        $checkInRooms = BookedRoom::with('device')->whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('company_id', $company_id);
            $q->where('booking_status', 2);
        })->get(["booking_id", "no_of_adult", "no_of_child", "no_of_baby"])->toArray();
        [
            $no_of_adult = array_column($checkInRooms, 'no_of_adult'),
            $no_of_child = array_column($checkInRooms, 'no_of_child'),
            $no_of_baby = array_column($checkInRooms, 'no_of_baby'),
        ];

        $dirtyRooms = Room::with(['device', 'bookedRoom'])
            ->whereHas('bookedRoom', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', 3);
                $q->where('company_id', $company_id);
                $q->orderBy('id', 'ASC');
            })

            ->with(['bookedRoom' => function ($q) use ($company_id) {
                $q->where("company_id", $company_id);
                $q->where('booking_status', 3);
                $q->with("customer");
            }]);

        //booking 1
        //checking 2
        //checkout 3
        //3 dirty
        //4 maintaining
        //0 available;

        // ======================

        // return $checkIn;

        return [
            'checkedOut' => $CheckedOut,
            'dirtyRooms' => $dirtyRooms->count(),
            'dirtyRoomsList' => $dirtyRooms->get(),
            'availableRooms' => $AvailableRooms,
            'confirmedBooking' => $confirmedBooking->count(),
            'blockedRooms' => $BlockedRooms,
            'confirmedBookingList' => $confirmedBooking->get(),
            'waitingBooking' => $waitingBooking,
            'expectCheckIn' => $expectCheckIn,
            'expectCheckOut' => $expectCheckOut,
            'reservedWithoutAdvance' => $reservedWithoutAdvance,

            'checkIn' => $checkIn,
            'checkOut' => $checkOut,

            'members' => [
                'adult' => array_sum($no_of_adult),
                'child' => array_sum($no_of_child),
                'baby' => array_sum($no_of_baby),
            ],

            'fooForCustomers' => $fooForCustomers,
            'status' => true,
        ];
    }

    public function getAvailableRoomsByDate(Request $request)
    {
        $model = BookedRoom::query();
        $roomIds = $model
            ->whereDate('check_in', '<=', $request->check_in)
            ->WhereDate('check_out', '>=', $request->check_out)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
            })
            ->pluck('room_id');
        return Room::whereNotIn('id', $roomIds)
            ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->where('company_id', $request->company_id)
            ->get();
    }

    public function getAvailableRoomsByDateAndRoomType(Request $request)
    {
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $company_id = $request->company_id;
        $roomTypeId = $request->room_type_id;

        return Room::with('device')
            ->where('company_id', $company_id)
            ->where('status', '!=', Room::Blocked)
            ->where('room_type_id', $roomTypeId)
            ->where('status', '!=', Room::Blocked)
            ->whereDoesntHave('bookedRoom', function ($q) use ($company_id, $checkIn, $checkOut) {
                $q->where('company_id', $company_id);

                // Option 1: Check for rooms that are not booked for the given date range
                $q->where(function ($query) use ($checkIn, $checkOut) {

                    $query->whereDate('check_in', '>=', $checkIn);
                    $query->whereDate("check_out", "<=", date('Y-m-d', strtotime($checkOut . " +1 day")));
                })
                    // Option 2: Exclude rooms based on booking status
                    ->orWhere(function ($query) use ($checkIn, $checkOut) {
                        // $query->whereDate('check_out', $todayDate);
                        $query->where('booking_status', 0);
                    });
            })
            ->get();
    }


    public function getAvailableRoomsForQuotation(Request $request)
    {
        return Room::whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->where('company_id', $request->company_id)
            ->where('status', '!=', Room::Blocked)
            ->where('room_type_id', $request->room_type_id)
            ->get();
    }

    public function getAvailableRoomsForModify(Request $request)
    {
        return Room::with("room_type")
            ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->when($request->filled("room_type_id"), fn($q) => $q->where("room_type_id", $request->room_type_id))
            ->where('company_id', $request->company_id)->get();
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
        return DB::table('food_prices')
            ->where('company_id', $request->company_id)
            ->get();
    }

    public function setRoomStatus($status, Request $request)
    {
        try {
            $msg = $status == 1 ? 'blocked' : 'unblocked';
            Room::whereCompanyId($request->company_id)->whereRoomNo($request->room_no)->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))->update(['status' => $status]);
            return $this->response('Room ' . $msg, null, true);
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, true);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsRoom = Room::where('company_id', $request->company_id)->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))->where('room_no', $request->room_no)->count();
                if ($verifyIsRoom == 0) {

                    $record = Room::create($data);

                    if ($record) {
                        return $this->response('Room details are successfully created', $record, true);
                    } else {
                        return $this->response('Room details not created', $record, false);
                    }
                } else {
                    return $this->response($request->room_no . ' : Room number is already exist. ', $data, false);
                }
            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show($id)
    {
        return Room::where('id', $id)->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))->first();
    }
    public function destroy(Room $lostAndFoundItems, $id)
    {
        if (Room::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
