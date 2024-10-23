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

        if ($request->filled("filter_date")) {
            $todayDate = $request->filter_date;
        } else {
            $todayDate = date('Y-m-d');
        }

        $company_id = $request->company_id;

        if ($request->filled("filter_date")) {
            $todayDate = $request->filter_date;
        } else {
            $todayDate = date('Y-m-d');
        }

        $AvailableRooms = Room::with("is_cleaned")->where('company_id', $company_id)->whereNot("status", Room::Blocked)->get();

        $expectCheckOut = Room::with('device')
            ->whereHas('bookedRoom', function ($query) use ($company_id, $todayDate) {
                $query->where('company_id', $company_id);
                $query->whereDate('check_out', '=', $todayDate);
                $query->where('booking_status', 2);
            })

            ->with(['bookedRoom' => function ($q) use ($company_id, $todayDate) {
                $q->with("customer");
                $q->where('company_id', $company_id);
                $q->whereDate('check_out', '=', $todayDate);
                $q->where('booking_status', 2);
            }])
            ->get();

        $continueRooms = Room::with('device')
            ->whereHas('bookedRoom', function ($query) use ($company_id, $todayDate) {
                $query->where('company_id', $company_id);
                $query->whereDate('check_out', '!=', $todayDate);
                $query->where('booking_status', 2);
            })

            ->with(['bookedRoom' => function ($q) use ($company_id, $todayDate) {
                $q->with("customer");
                $q->where('company_id', $company_id);
                $q->whereDate('check_out', $todayDate);
                $q->where("booking_status", ">", 0);
            }])
            ->get();

        $reservedWithoutAdvance = Room::whereHas('bookedRoom', function ($q) use ($company_id, $todayDate) {
            $q->whereNotNull('room_id');
            $q->where('company_id', $company_id);


            $q->where(function ($query) use ($todayDate) {
                // Check if the check-in is before or equal to today, and check-out is after or equal to today
                $query->whereDate('check_in', '<=', $todayDate)
                    ->whereDate('check_out', '>=', $todayDate)
                    ->where('booking_status', BookedRoom::BOOKED) // Status for dirty rooms
                    ->where('booking_status', '!=', 0); // Exclude non-active bookings
            });
        })
            ->with(['is_cleaned', 'device', 'bookedRoom' => function ($q) use ($company_id, $todayDate) {

                $q->whereNotNull('room_id');
                $q->where('company_id', $company_id);

                $q->where(function ($query) use ($todayDate) {
                    // Check if the check-in is before or equal to today, and check-out is after or equal to today
                    $query->whereDate('check_in', '<=', $todayDate)
                        ->whereDate('check_out', '>=', $todayDate)
                        ->where('booking_status', BookedRoom::BOOKED) // Status for dirty rooms
                        ->where('booking_status', '!=', 0); // Exclude non-active bookings
                });

                $q->with("customer");
            }])
            ->get();

        $BlockedRooms = Room::with('device', "is_cleaned")->where("status", Room::Blocked)->where('company_id', $company_id)->get();

        $Occupied = Room::with('device', 'is_cleaned')
            // ->whereHas('roomType', fn ($q) => $q->where('type', request("type", "room")))
            ->whereHas('bookedRoom', function ($query) use ($company_id, $todayDate) {
                $query->whereDate('check_in', $todayDate);
                $query->where('company_id', $company_id);
                $query->where('booking_status', 2);
            })

            ->with(['bookedRoom' => function ($q) use ($company_id, $todayDate) {

                $q->whereNotNull('room_id');
                $q->where('company_id', $company_id);

                $q->where(function ($query) use ($todayDate) {
                    // Check if the check-in is before or equal to today, and check-out is after or equal to today
                    $query->whereDate('check_in', '<=', $todayDate)
                        ->whereDate('check_out', '>=', $todayDate)
                        ->where('booking_status', BookedRoom::CHECKED_IN)
                        ->where('booking_status', '!=', 0);
                });

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




        $dirtyRooms = Room::with(['device', 'bookedRoom', "is_cleaned"])
            ->whereHas('bookedRoom', function ($q) use ($company_id, $todayDate) {
                $q->where('company_id', $company_id)
                    ->where(function ($query) use ($todayDate) {
                        // Check if the check-in is before or equal to today, and check-out is after or equal to today
                        $query->whereDate('check_in', '<=', $todayDate)
                            ->whereDate('check_out', '>=', $todayDate)
                            ->where('booking_status', 3) // Status for dirty rooms
                            ->where('booking_status', '!=', 0); // Exclude non-active bookings
                    });
            })
            ->with(['bookedRoom' => function ($q) use ($company_id) {
                $q->where("company_id", $company_id)
                    ->where('booking_status', 3) // Only consider dirty rooms
                    ->with("customer");
            }])->get();

        $inHouseData = BookedRoom::where('company_id', $company_id)
            ->where(function ($query) use ($todayDate) {
                $query->whereDate('check_out', $todayDate)
                    ->orWhereDate('check_in', $todayDate);
            })
            ->where('booking_status', 2)
            ->selectRaw("
            SUM(CASE WHEN DATE(check_out) = ? THEN no_of_adult ELSE 0 END) as expected_adult,
            SUM(CASE WHEN DATE(check_out) = ? THEN no_of_child ELSE 0 END) as expected_child,
            SUM(CASE WHEN DATE(check_in) = ? THEN no_of_adult ELSE 0 END) as occupied_adult,
            SUM(CASE WHEN DATE(check_in) = ? THEN no_of_child ELSE 0 END) as occupied_child
        ", [$todayDate, $todayDate, $todayDate, $todayDate])
            ->first();


        $expectedAdult = $inHouseData->expected_adult ?? 0;
        $expectedChild = $inHouseData->expected_child ?? 0;

        $occupiedAdult = $inHouseData->occupied_adult ?? 0;
        $occupiedChild = $inHouseData->occupied_child ?? 0;

        $totalAdult = $expectedAdult + $occupiedAdult;
        $totalChild = $expectedChild + $occupiedChild;

        $membersCount = [
            "adult" => $totalAdult,
            "child" => $totalChild,
            "total" => $totalAdult + $totalChild,
        ];



        $FoodOrder = BookedRoom::where('company_id', $company_id)
            ->where(function ($query) use ($todayDate) {
                $query->whereDate('check_out', $todayDate)
                    ->orWhereDate('check_in', $todayDate);
            })
            ->whereIn('booking_status', [1, 2])
            ->selectRaw("
            SUM(CASE WHEN DATE(check_out) = ? THEN breakfast ELSE 0 END) as expected_breakfast,
            SUM(CASE WHEN DATE(check_out) = ? THEN lunch ELSE 0 END) as expected_lunch,
            SUM(CASE WHEN DATE(check_out) = ? THEN dinner ELSE 0 END) as expected_dinner,
            SUM(CASE WHEN DATE(check_in) = ? THEN breakfast ELSE 0 END) as occupied_breakfast,
            SUM(CASE WHEN DATE(check_in) = ? THEN lunch ELSE 0 END) as occupied_lunch,
            SUM(CASE WHEN DATE(check_in) = ? THEN dinner ELSE 0 END) as occupied_dinner
        ", [$todayDate, $todayDate, $todayDate, $todayDate, $todayDate, $todayDate])
            ->first();

        $expectedBreakfast = $FoodOrder->expected_breakfast ?? 0;
        $expectedLunch = $FoodOrder->expected_lunch ?? 0;
        $expectedDinner = $FoodOrder->expected_dinner ?? 0;

        $occupiedBreakfast = $FoodOrder->occupied_breakfast ?? 0;
        $occupiedLunch = $FoodOrder->occupied_lunch ?? 0;
        $occupiedDinner = $FoodOrder->occupied_dinner ?? 0;

        $totalBreakfast = $expectedBreakfast + $occupiedBreakfast;
        $totalLunch = $expectedLunch + $occupiedLunch;
        $totalDinner = $expectedDinner + $occupiedDinner;

        $foodOrdersCount = [
            "breakfast" => $totalBreakfast,
            "lunch" => $totalLunch,
            "dinner" => $totalDinner,
            "total" => $totalBreakfast + $totalLunch + $totalDinner,
        ];

        return [
            'allRooms' => $AvailableRooms,
            'availableRooms' => $AvailableRooms,
            'reservedWithoutAdvance' => $reservedWithoutAdvance,
            'bookedRooms' => $reservedWithoutAdvance,
            'checkIn' => $Occupied,
            'expectCheckOut' => $expectCheckOut,
            'continueRooms' => $continueRooms,
            'dirtyRoomsList' => $dirtyRooms,
            'blockedRooms' => $BlockedRooms,
            'checkOut' => $checkOut,
            'members' => $membersCount,
            'foodOrdersCount' => $foodOrdersCount,
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

        $model = BookedRoom::query();
        $roomIds = $model
            ->whereDate('check_in', '<=', $checkIn)
            ->whereDate('check_out', '>=', $checkOut)
            ->where('booking_status', '!=', 0)
            ->pluck('room_id');

        return Room::whereNotIn('id', $roomIds)
            // ->whereHas('roomType', fn($q) => $q->where('type', request("type", "room")))
            ->where('room_type_id', $roomTypeId)
            ->where('company_id', $company_id)
            ->with('device')
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
