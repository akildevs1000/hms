<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Room;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use Carbon\CarbonPeriod;
use App\Jobs\WhatsappJob;
use App\Models\OrderRoom;
use App\Models\BookedRoom;
use App\Models\CancelRoom;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Booking\StoreRequest;
use App\Http\Requests\Booking\BookingRequest;
use Illuminate\Support\Facades\Log as Logger;



class TestController extends Controller
{

    public function booking_validate(BookingRequest $request)
    {
        return $this->response('Booking validated.', null, true);
    }

    public function customerStore($customer)
    {
        try {
            $isExistCustomer =   Customer::whereContactNo($customer['contact_no'])->first();
            $id = "";
            if ($isExistCustomer) {
                $id = $isExistCustomer->id;
                $isExistCustomer->update($customer);
            } else {
                $record = Customer::create($customer);
                $id = $record->id;
            }
            return $id;
            return $this->response('Customer successfully added.', $id, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function store(Request $request)
    {
        return DB::transaction(function ()  use ($request) {
            $customer_id = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            $booking = $this->storeBooking($request);
            if ($booking) {
                $this->storeBookedRooms($request, $booking);
                return response()->json(['data' => $booking->id, 'status' => true]);
            }
        });
    }

    public function storeBooking($request)
    {
        try {
            return   DB::transaction(function () use ($request) {
                $data                   = [];
                $data                   = $request->only(Booking::bookingAttributes());
                $data['booking_date']   = now();
                $data['payment_status'] = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
                $data['remaining_price'] = (float)$request->total_price - (float)$request->advance_price;
                $data['grand_remaining_price'] = (int)$request->total_price - (float)$request->advance_price;

                return  $booked  = Booking::create($data);

                if ($booked) {

                    Food::create([
                        'booking_id' => $booked->id,
                        'breakfast' => $request->json('qty_breakfast'),
                        'lunch' => $request->json('qty_lunch'),
                        'dinner' => $request->json('qty_dinner'),
                    ]);


                    $transactionData = [
                        'booking_id' => $booked->id,
                        'customer_id' => $booked->customer_id ?? '',
                        'date' => now(),
                        'company_id' => $request->company_id ?? '',
                        'payment_method_id' => $booked->payment_mode_id,
                        'desc' => 'rooms booking amount',
                    ];

                    //Transaction
                    $payment = new TransactionController();
                    $payment->store($transactionData, $request->total_price, 'debit');

                    if ($request->advance_price && $request->advance_price > 0) {
                        $transactionData['desc'] = 'advance payment';
                        $payment->store($transactionData, $request->advance_price, 'credit');
                    }
                    //End Transaction
                    if ((float)$booked->advance_price == 0) {

                        if (($booked->paid_by && $booked->paid_by == 2) || ($booked->type != 'Walking' && $booked->type != 'Complimentary')) {

                            $agentsData = [
                                'booking_id'   => $booked->id,
                                'customer_id'  => $booked->customer_id ?? '',
                                'type'         => $booked->type ?? '',
                                'source'       => $booked->source,
                                'reference_no' => $booked->reference_no ?? '',
                                'amount'       => $booked->total_price ?? '',
                                'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                                'company_id'   => $request->company_id ?? '',
                                'is_paid'      => $booked->paid_by == 1 ? 2 : 0,
                            ];
                            $payment = new AgentsController();
                            $payment->store($agentsData);

                            $paymentsData = [
                                'booking_id'   => $booked->id,
                                'payment_mode' => 7,
                                'description'  => 'booked from ' . $booked->source,
                                'amount'       =>   $booked->remaining_price,
                                'type'         => 'room',
                                'room'         => $booked->rooms,
                                'company_id'   => $request->company_id,
                                'is_city_ledger'  => 1,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        } else {
                            $agentsData = [
                                'booking_id'   => $booked->id,
                                'customer_id'  => $booked->customer_id ?? '',
                                'type'         => 'Customer' ?? '',
                                'source'       => $booked->source,
                                'reference_no' => $booked->reference_no ?? '',
                                'amount'       => $booked->total_price ?? '',
                                'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                                'company_id'   => $request->company_id ?? '',
                            ];
                            $payment = new AgentsController();
                            $payment->store($agentsData);


                            $paymentsData = [
                                'booking_id'   => $booked->id,
                                'payment_mode' => 7,
                                'description'  => 'booked from ' . $booked->source,
                                'amount'       =>   $booked->remaining_price,
                                'type'         => 'room',
                                'room'         => $booked->rooms,
                                'company_id'   => $request->company_id,
                                'is_city_ledger'  => 1,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        }
                    } else {

                        if ($request->total_price >= $request->advance_price) {

                            $paymentsData = [
                                'booking_id'   => $booked->id,
                                'payment_mode' => $booked->payment_mode_id,
                                'description'  => 'advance payment',
                                'amount'       =>   $booked->advance_price,
                                'type'         => 'room',
                                'room'         => $booked->rooms,
                                'company_id'   => $request->company_id,
                                'is_city_ledger'  => 0,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        }

                        $paymentsData = [
                            'booking_id'   => $booked->id,
                            'payment_mode' => 7,
                            'description'  => 'pending payment',
                            'amount'       =>   $booked->remaining_price,
                            'type'         => 'room',
                            'room'         => $booked->rooms,
                            'company_id'   => $request->company_id,
                            'is_city_ledger'  => 1,
                        ];
                        $payment = new PaymentController();
                        $payment->store($paymentsData);

                        $agentsData = [
                            'booking_id'   => $booked->id,
                            'customer_id'  => $booked->customer_id ?? '',
                            'type'         => 'Customer' ?? '',
                            'source'       => $booked->source,
                            'reference_no' => $booked->reference_no ?? '',
                            'amount'       => $booked->total_price ?? '',
                            'agent_paid_amount'       => $booked->advance_price ?? '',
                            'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                            'company_id'   => $request->company_id ?? '',
                        ];
                        $payment = new AgentsController();
                        $payment->store($agentsData);
                    }
                }

                return $booked;

                return $this->response('Room Booked Successfully.', $booked, true);
            });
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function storeBookedRooms($request, $booking)
    {
        try {
            $rooms   = $request->only('selectedRooms');
            foreach ($rooms['selectedRooms'] as $room) {
                $room['booking_id'] = $booking->id;
                $room['customer_id'] = $booking->customer_id;
                $bookedRoomId = BookedRoom::create($room);
                $period       = CarbonPeriod::create($room['check_in'], $this->checkOutDate($room['check_out']));
                foreach ($period as $date) {
                    $room['date']           = $date->format('Y-m-d');
                    $room['booked_room_id'] = $bookedRoomId->id;
                    OrderRoom::create($room);
                }
            }

            // $data = [
            //     "from" => "14157386102",
            //     "to" => "971502848071",
            //     "message_type" => "text",
            //     "text" => "you have to " . count($rooms) . " rooms booking \nyour reservation number is " . $rooms[0]['booking_id'],
            //     "channel" => "whatsapp"
            // ];

            // WhatsappJob::dispatch($data);
            return $rooms;
            return $this->response('Room Booked Successfully.', $rooms, true);
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function storeDocument($request, $booking)
    {
        $booking    = Booking::find($booking->id);
        $customer    = Customer::find($booking->customer_id);
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path = $file->storeAs('public/documents/booking', $fileName);
            Storage::copy($path, 'public/documents/customer/' . $fileName);
            $booking->document = $fileName;
            $customer->document = $fileName;
        } else {
            $booking->document = $customer->document_name ?? null;
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;

            $path = $file->storeAs('public/documents/customer/photo', $fileName);
            $customer->image = $fileName;
        }
        $booking->save();
        $customer->save();
        return $this->response('Room Booked Successfully.', null, true);
    }

    private function checkOutDate($date)
    {
        $date = date_create($date);
        date_modify($date, "-1 days");
        return date_format($date, "Y-m-d");
    }
}