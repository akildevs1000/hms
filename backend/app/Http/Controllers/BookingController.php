<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreRequest;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return Booking::with(["customer:id,name", "room"])->where('company_id', $request->company_id)->orderByDesc("id")->paginate($request->per_page ?? 50);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {

        DB::beginTransaction();

        try {
            $customer_id = $request->customer_id;

            if (!$customer_id) {
                $customer    = new CustomerController();
                $customer    = $customer->store($request);
                $customer_id = $customer->id;
            }

            $data = $request->validated();

            $data['customer_id'] = $customer_id;
            $data['total_price'] = $request->total_price;
            $data['remaining_price'] = $request->remaining_price;
            $data['booking_date'] = now();
            $data['payment_status'] = $request->total_price == $request->remaining_price ? '0' : '1';

            $booked = Booking::create($data);

            if ($booked) {

                $room    = new RoomController();
                $updated = $room->update($request->room_id, '1'); //1 = booked

                if ($updated) {
                    DB::commit();
                    return response()->json(['status' => true, 'message' => 'Room Booked Successfully']);
                } else {
                    DB::rollBack();
                    return ["done" => false, "data" => "DataBase Error in status change"];
                }
            } else {
                DB::rollBack();
                return ["done" => false, "data" => "DataBase Error booking"];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function check_in_room(Request $request)
    {
        $booking_id      = $request->booking_id;
        $advance_payment = $request->advance_payment;

        if ($booking_id == "") {
            return [
                'done' => false,
                'data' => "Error With Booking",
            ];
        }

        $booking_details = Booking::where('booking_id', $booking_id)->first();
        $room_id         = $booking_details->room_id;
        $remaining_price = $booking_details->total_price - $advance_payment;

        $updated = Booking::where('booking_id', $booking_id)->update(["remaining_price" => $remaining_price]);

        if (!$updated) {
            return [
                'done' => false,
                'data' => "Problem in payment",
            ];
        }

        $updateRoom = Room::where('room_id', $room_id)->update(["check_in_status" => '1']);

        return $updateRoom ? ['done' => true, 'data' => ''] : ['done' => false, 'data' => "Problem in Update Room Check in status"];
    }

    public function check_out_room(Request $request)
    {

        $booking_id       = $request->booking_id;
        $remaining_amount = $request->remaining_amount;

        if ($booking_id == "") {
            return [
                'done' => false,
                'data' => "Error With Booking",
            ];
        }

        $booking_details = Booking::where('booking_id', $booking_id)->first();
        $room_id         = $booking_details->room_id;
        $remaining_price = $booking_details->remaining_price;

        if ($remaining_price !== $remaining_amount) {

            return [
                'done' => false,
                'data' => "Please Enter Full Payment",
            ];
        }
        $updateBooking = Booking::where('booking_id', $booking_id)->update(["remaining_price" => '0', 'payment_status' => '1']);

        if (!$updateBooking) {
            return [
                'done' => false,
                'data' => "Problem in payment",
            ];
        }

        $updateRoom = Room::where('room_id', $room_id)->update(["check_in_status" => '0', 'check_out_status' => '1', 'status' => null]);

        return $updateRoom ? ['done' => true, 'data' => ''] : ['done' => false, 'data' => "Problem in Update Room Check in status"];
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
        return Booking::where('company_id', $request->company_id)->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }
}
