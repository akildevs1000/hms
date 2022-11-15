<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logger;

use App\Http\Requests\Booking\StoreRequest;


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
    public function booking_validate(StoreRequest $request)
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

            Booking::create($data);
            $room  = new RoomController();
            $updated = $room->update($request->room_id, 1); //1 = booked

            if ($updated) {
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

    public function check_in_room(Request $request)
    {
        try {
            $booking_id      = $request->booking_id;
            $booking = Booking::find($booking_id);
            $booking->remaining_price = $request->remaining_price;
            $booking->payment_mode_id = $request->payment_mode_id;
            $booking->advance_price = $request->advance_price;
            $booking->booking_status = 2;

            $booking->save();

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
            $booking_id      = $request->booking_id;
            $booking = Booking::find($booking_id);
            $booking->remaining_price = $request->remaining_price;
            $booking->payment_mode_id = $request->payment_mode_id;
            $booking->advance_price = $request->advance_price;

            if ($booking->save()) {
                return response()->json(['data' => '', 'message' => 'Payment Successfully', 'status' => true]);
            }
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_out_room(Request $request)
    {
        // return $request->all();
        $booking_id       = $request->booking_id;

        $booking = Booking::find($booking_id);
        $booking->full_payment = $request->full_payment;
        $booking->payment_mode_id = $request->payment_mode_id;
        $booking->booking_status = 3;
        $booking->save();
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
        return Booking::where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }

    public function get_booking_by_check_in(Request $request)
    {
        return Booking::with('room')
            ->find($request->id);
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
}