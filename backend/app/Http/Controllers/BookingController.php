<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Billing;
use App\Models\Booking;
use Carbon\CarbonPeriod;
use App\Models\BookedRoom;

use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Booking\StoreRequest;
use App\Http\Requests\Booking\BookingRequest;
use App\Models\OrderRoom;
use Illuminate\Support\Facades\Log as Logger;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Booking::with(["customer:id,first_name,last_name", "room"])
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->orderByDesc("id")
            ->paginate($request->per_page ?? 50);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function booking_validate(BookingRequest $request)
    {
        return $this->response('Booking validated.', null, true);
    }
    public function updateByDrag(Request $request)
    {
        // return $request->only('check_in', 'check_out', 'id');
        return Booking::where('id', $request->id)->update($request->only('check_in', 'check_out'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->except(['room_type', 'amount', 'price']);
            $data["customer_id"] = $request->customer_id;
            $data['booking_date'] = now();
            $data['payment_status'] = $request->total_price == $request->remaining_price ? '0' : '1';

            // $room  = new RoomController();
            // $room->update($request->room_id, 1);
            $booked =  Booking::create($data);

            if (now() <= $booked->check_in) {
                $room  = new RoomController();
                $room->update($request->room_id, 1);
            } else {
                $room  = new RoomController();
                $room->update($request->room_id, 0);
            }

            if ($booked) {
                return $this->response('Room Booked Successfully.', null, true);
            } else {
                return $this->response('DataBase Error in status change', null, true);
            }
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    // public function storeBulk(StoreRequest $request)
    // {
    //     try {
    //         $data = $request->except(['room_type', 'amount', 'price']);
    //         $data["customer_id"] = $request->customer_id;
    //         $data['booking_date'] = now();
    //         $data['payment_status'] = $request->total_price == $request->remaining_price ? '0' : '1';

    //         // $room  = new RoomController();
    //         // $room->update($request->room_id, 1);
    //         $booked =  Booking::create($data);

    //         if (now() <= $booked->check_in) {
    //             $room  = new RoomController();
    //             $room->update($request->room_id, 1);
    //         } else {
    //             $room  = new RoomController();
    //             $room->update($request->room_id, 0);
    //         }

    //         if ($booked) {
    //             return $this->response('Room Booked Successfully.', null, true);
    //         } else {
    //             return $this->response('DataBase Error in status change', null, true);
    //         }
    //     } catch (\Throwable $th) {
    //         return $th;
    //         Logger::channel("custom")->error("BookingController: " . $th);
    //         return ["done" => false, "data" => "DataBase Error booking"];
    //     }
    // }

    public function check_in_room(Request $request)
    {

        try {
            $booking_id = $request->booking_id;
            $booking = Booking::find($booking_id);
            $rem = (int)$request->remaining_price - (int)$request->new_payment;
            $booking->remaining_price = $rem;
            $booking->check_in_price = $request->new_payment;
            $booking->payment_mode_id = $request->payment_mode_id;
            $booking->booking_status = 2;

            $booking->save();
            return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);

            if ($booking->save()) {
                Room::where('id', $booking->room_id)->update(["status" => '2']);
                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function payingAdvance(Request $request)
    {
        try {
            $booking_id = $request->booking_id;
            $booking = Booking::find($booking_id);
            $rem = (int)$request->remaining_price - (int)$request->new_advance;
            $booking->remaining_price = $rem;
            $booking->advance_price = $request->new_advance;
            $booking->payment_mode_id = $request->payment_mode_id;

            if ($booking->save()) {
                return response()->json(['data' => '', 'message' => 'Payment Successfully', 'status' => true]);
            }
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function roomDetails($id)
    {
        return BookedRoom::find($id);
    }


    public function check_out_room(Request $request)
    {

        $booking_id = $request->booking_id;
        $booking = Booking::find($booking_id);

        if ($booking->remaining_price <=  $request->full_payment) {
            $booking->remaining_price = 0;
            $booking->payment_status = 1;
            $booking->full_payment = $booking->total_price;
        } else {
            $booking->remaining_price =  ((int)$booking->total_price - (int)$request->full_payment);
        }
        $booking->payment_mode_id = $request->payment_mode_id;
        $booking->check_in_price = $request->full_payment;
        $booking->booking_status = 3;
        $booking->save();

        return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);

        if ($booking->save()) {
            Room::where('id', $booking->room_id)->update(["status" => '3']);
            return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
        }
        return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function booked_room($room_id)
    {
        $data = DB::table('rooms')
            ->join('room_types', 'room_types.id', '=', 'rooms.room_type_id')
            ->join('bookings', 'bookings.room_id', '=', 'rooms.id')
            ->join('customers', 'customer.id', '=', 'bookings.customer_id')
            ->where('rooms.id', $room_id)
            ->where('bookings.payment_status', '0')
            ->get(['booking_id', 'customer_name as name', 'room_no', 'room_type', 'check_in', 'check_in', 'total_price', 'remaining_price']);

        if (!$data) {
            return [
                'done' => false,
                'data' => "DataBase Error",
            ];
        }

        $data["done"] = true;

        $data['check_in']  = date('M j, Y', strtotime($data['check_in']));
        $data['check_out'] = date('M j, Y', strtotime($data['check_in']));

        return $data;

        // $sql = "SELECT * FROM room NATURAL JOIN room_type NATURAL JOIN booking NATURAL JOIN customer WHERE room_id = '$room_id' AND payment_status = '0'";
        // $result = mysqli_query($connection, $sql);
        // if ($result) {
        //     $room = mysqli_fetch_assoc($result);
        //     $response['done'] = true;
        //     $response['booking_id'] = $room['booking_id'];
        //     $response['name'] = $room['customer_name'];
        //     $response['room_no'] = $room['room_no'];
        //     $response['room_type'] = $room['room_type'];
        //     $response['check_in'] = date('M j, Y', strtotime($room['check_in']));
        //     $response['check_out'] = date('M j, Y', strtotime($room['check_in']));
        //     $response['total_price'] = $room['total_price'];
        //     $response['remaining_price'] = $room['remaining_price'];
        // } else {
        //     $response['done'] = false;
        //     $response['data'] = "DataBase Error";
        // }

        // echo json_encode($response);
    }

    public function customer_details($room_id)
    {

        if ($room_id == '') {
            return [
                'done' => false,
                'data' => "DataBase Error",
            ];
        }

        $data = DB::table('rooms')
            ->join('room_types', 'room_types.id', '=', 'rooms.room_type_id')
            ->join('bookings', 'bookings.room_id', '=', 'rooms.id')
            ->join('customers', 'customer.id', '=', 'bookings.customer_id')
            ->where('rooms.id', $room_id)
            ->where('bookings.payment_status', '0')
            ->first();

        if (!$data) {
            return [
                'done' => false,
                'data' => "DataBase Error",
            ];
        }

        $id_type_name = DB::table('id_card_types')->where('id', $data->id_card_type_id)->first('id');

        $data['id_card_type_id'] = $id_type_name['id_card_type'];

        $data['done'] = true;

        return $data;
    }

    public function events_list(Request $request)
    {

        return BookedRoom::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0);
        })->get(['id', 'room_id', 'booking_id', 'customer_id', 'check_in as start', 'check_out as end']);

        // return Booking::where('company_id', $request->company_id)
        //     ->where('booking_status', '!=', 0)
        //     ->with('bookedRooms')
        //     ->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);


    }

    public function events_list1(Request $request)
    {
        return Booking::where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }


    public function get_booking(Request $request)
    {
        $bookedRoom = BookedRoom::with('booking')->find($request->id);
        $bookedRoom->booking->room_id = $bookedRoom->room_id;
        $bookedRoom->booking->room_no = $bookedRoom->room_no;
        $bookedRoom->booking->room_type = $bookedRoom->room_type;
        return $bookedRoom->booking;

        //old code
        // return Booking::with('room')
        //     ->find($request->id);
    }

    public function cancelReservation(Request $request, $id)
    {
        try {
            $booking =  Booking::find($id);
            $booking->update([
                'booking_status' => 0,
                'reason' => $request->reason,
                'user_id' => $request->cancel_by,
                'cancel_date' => now(),
            ]);
            if ($booking) {
                Room::where('id', $booking->room_id)->update(["status" => '0']);
                return $this->response('Room cancel Successfully.', null, true);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setAvailable($id)
    {
        try {
            $booking =  Booking::find($id);
            $booking->update([
                'booking_status' => 0,
            ]);
            if ($booking) {
                Room::where('id', $booking->room_id)->update(["status" => '0']);
                return $this->response('Now room available.', null, true);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setMaintenance($id)
    {
        try {
            $booking =  Booking::find($id);
            $booking->update([
                'booking_status' => 5,
            ]);
            if ($booking) {
                Room::where('id', $booking->room_id)->update(["status" => '5']);
                return $this->response('Now room maintenance.', null, true);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getEventsByRoom(Request $request)
    {
        $model = Booking::query();

        return $model
            ->where('room_id', Room::whereRoomId($request->room_no)->first())
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }

    public function changeRoomByDrag(Request $request)
    {

        try {
            return $booked = Booking::find($request->eventId);
            $room   =  Room::whereRoomNo($request->roomId)->first();

            $roomId = $room->id;

            $booked =   $booked->update([
                'check_in' => $request->start,
                'check_out' => $request->end,
                'room_id' => $roomId,
            ]);

            // $room  = new RoomController();
            // $room->update($roomId, 1);

            if (now() >= $request->start) {
                $room  = new RoomController();
                $room->update($roomId, 1);
            } else {
                $room  = new RoomController();
                $room->update($roomId, 0);
            }

            if ($booked) {
                return $this->response('Room changed Successfully.', null, true);
            } else {
                return $this->response('DataBase Error in status change', null, true);
            }
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function changeDateByDrag(Request $request)
    {
        try {
            $booked = Booking::find($request->eventId);
            $roomId =  Room::whereRoomNo($request->roomId)->first()->id ?? '';
            $booked =   $booked->update([
                'check_in' => $request->start,
                'check_out' => $request->end,
                'room_id' => $roomId,
            ]);

            if (now() >= $request->start) {
                $room  = new RoomController();
                $room->update($roomId, 1);
            }

            if ($booked) {
                return $this->response('Date changed Successfully.', null, true);
            } else {
                return $this->response('DataBase Error in status change', null, true);
            }
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function store1(Request $request)
    {

        try {
            $data = [];
            // $data =  $request->all();
            $data =  $request->except('document');
            $data["customer_id"] = $request->customer_id;
            $data['booking_date'] = now();
            $data['payment_status'] = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';

            // $room  = new RoomController();
            // $room->update($request->room_id, 1);

            $booked =  Booking::create($data);

            // {
            //     "customer_type": "Regular",
            //     "customer_status": "Confirmed",
            //     "all_room_Total_amount": 4484,
            //     "total_extra": 0,
            //     "type": "Walking",
            //     "source": "",
            //     "agent_name": "",
            //     "check_in": "2022-12-19",
            //     "check_out": "2022-12-21",
            //     "discount": 0,
            //     "advance_price": 0,
            //     "payment_mode_id": 1,
            //     "total_days": 3,
            //     "sub_total": 13452,
            //     "after_discount": 0,
            //     "sales_tax": 0,
            //     "total_price": 13452,
            //     "remaining_price": 13452,
            //     "request": "AC",
            //     "company_id": 1,
            //     "remark": "",
            //     "customer_id": 116
            // }

            return $this->response('Room Booked Successfully.', $booked, true);

            if (now() <= $booked->check_in) {
                $room  = new RoomController();
                $room->update($request->room_id, 1);
            } else {
                $room  = new RoomController();
                $room->update($request->room_id, 0);
            }

            if ($booked) {
                return $this->response('Room Booked Successfully.', null, true);
            } else {
                return $this->response('DataBase Error in status change', null, true);
            }
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function storeDocument(Request $request)
    {
        $booking_id = $request->booking_id;
        $booking = Booking::find($booking_id);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $request->document->move(public_path('documents/booking/'), $fileName);
            // $data['document'] = $fileName;
            $booking->document = $fileName;
            $booking->save();
            return $this->response('Room Booked Successfully.', null, true);
        }
    }

    public function storeBookedRooms(Request $request)
    {
        try {
            $rooms = $request->all();
            foreach ($rooms as $room) {
                BookedRoom::create($room);
                $period = CarbonPeriod::create($room['check_in'], $room['check_out']);
                foreach ($period as $date) {
                    $room['date'] = $date->format('Y-m-d');
                    OrderRoom::create($room);
                }
            }

            return $this->response('Room Booked Successfully.', $rooms, true);

            // $data = $request->except(['room_type', 'amount', 'price']);
            // $data["customer_id"] = $request->customer_id;
            // $data['booking_date'] = now();
            // $data['payment_status'] = $request->total_price == $request->remaining_price ? '0' : '1';

            // $room  = new RoomController();
            // $room->update($request->room_id, 1);
            // $booked =  Booking::create($data);

            // if (now() <= $booked->check_in) {
            //     $room  = new RoomController();
            //     $room->update($request->room_id, 1);
            // } else {
            //     $room  = new RoomController();
            //     $room->update($request->room_id, 0);
            // }

            // if ($booked) {
            //     return $this->response('Room Booked Successfully.', null, true);
            // } else {
            //     return $this->response('DataBase Error in status change', null, true);
            // }
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }


    public function reservationList(Request $request)
    {
        $model = Booking::query()
            ->latest()
            ->filter(request('search'));

        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name'
            ])
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->paginate($request->per_page ?? 20);
    }


    // public function generateBill(Type $var = null)
    // {
    //     # code...
    // }
}