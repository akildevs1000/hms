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
use App\Models\IdCardType;
use Illuminate\Support\Facades\Log as Logger;

class BookingController extends Controller
{

    public function index(Request $request)
    {
        return Booking::with(["customer:id,first_name,last_name", "room"])
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->orderByDesc("id")
            ->paginate($request->per_page ?? 50);
    }

    public function booking_validate(BookingRequest $request)
    {
        return $this->response('Booking validated.', null, true);
    }

    public function customerStore($customer)
    {
        try {
            $isExistCustomer =   Customer::whereContactNo($customer['contact_no'])->whereCompanyId($customer['company_id'])->first();
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

    public function getReservationNumber($data)
    {
        $company_id = $data['company_id'];
        $starting_value = 00001;
        $model = Booking::query();

        (int)$counter = $model->where('company_id', $company_id)->latest('reservation_no')->value('reservation_no') ?? $starting_value;
        $exist = $model->where('company_id', $company_id)->where('reservation_no', $counter)->exists();

        if ($exist) {
            return   (int)++$counter;
        } else {
            return $starting_value;
        }
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
                $data['reservation_no']  = $this->getReservationNumber($data);

                $booked  = Booking::create($data);

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

                    (new TaxableController)->storeTaxableInvoice($booked);
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

            $customer = Customer::find($booking->customer_id);
            $this->whatsappNotification($booking, $rooms['selectedRooms'], $customer, 'booking');

            return $rooms;
            return $this->response('Room Booked Successfully.', $rooms, true);
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    private function whatsappNotification($booking, $rooms, $customer, $type)
    {
        $instance_id = "";
        $access_token = "";
        $msg = "";
        $numberOfRooms = count($rooms);
        $customerName = ucfirst($customer['first_name']) ?? 'Guest';
        $checkIn = date('d-M-y', strtotime($booking->check_in));
        $checkOut = date('d-M-y H:i', strtotime($booking->check_out));
        $days = $booking->total_days;
        $bookedRooms = $booking->rooms;
        $totalAmount = $booking->total_price;
        $advanceAmount = $booking->advance_price;
        $remainingAmount = $booking->remaining_price;
        $company_id = $booking->company_id;
        $appName = env('APP_NAME');

        if ($company_id == 1) {
            $location = "  https://goo.gl/maps/gA9h3YxGTwLaRETG6";
            $company = 1;
            $instance_id = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $comName = "Thanjavur";
        } else if ($company_id == 2) {
            $location = "  https://goo.gl/maps/bNznm2Z4pbxo2ZJw9";
            $company = 2;
            $instance_id = "KODAI_INSTANCE_ID";
            $access_token = "KODAI_ACCESS_TOKEN";
            $comName = "Kodaikanal";
        }

        $msg .= "Dear $customerName, \n";

        $msg .= "Welcome to HYDERS PARK The Luxury Hotel, $comName, \n";
        $msg .= "Your booking reference number, $booking->id, \n";
        $msg .= "Number of Rooms, $numberOfRooms, \n";

        $msg .= "Check In/Out , $checkIn to $checkOut, \n";
        $msg .= "Your total bill is ₹$totalAmount \n";
        $msg .= "You paid advance ₹$advanceAmount \n";
        $msg .= "Your remaining amount is ₹$remainingAmount \n";

        if ($advanceAmount <= 0) {
            $msg .= "You must pay an advance within 48 hours to confirm your booking.\n";
        }


        $msg .= "\n";
        $msg .= "Further information can   be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        $msg .= "\n";
        $msg .= "\n";

        // $msg .= "About us.\n";
        $msg .= "Google Map  $location.\n";
        $msg .= "\n";
        $msg .= "\n";
        $msg .= "More https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels.\n";

        $data = [
            'to' => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message' => $msg,
            'company' => $company ?? false,
            'instance_id' => $instance_id,
            'access_token' => $access_token,
        ];
        (new WhatsappController)->sentNotification($data);
    }

    private function checkInNotification($booking, $customer)
    {
        $instance_id = "";
        $access_token = "";
        $comName = "";
        $location = "";
        $video = "";
        $msg = "";
        $customerName = ucfirst($customer['first_name']) ?? 'Guest';
        $checkOut = date('d-M-y H:i', strtotime($booking->check_out));
        $company_id = $booking->company_id;

        if ($company_id == 1) {
            $location = "  https://goo.gl/maps/gA9h3YxGTwLaRETG6";
            $company = 1;
            $instance_id = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $video = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
            $comName = "Thanjavur";
        } else if ($company_id == 2) {
            $location = "  https://goo.gl/maps/bNznm2Z4pbxo2ZJw9";
            $company = 2;
            $instance_id = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $comName = "Kodaikanal";
            $video = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
        }

        $msg .= "Dear $customerName, \n";

        $msg .= "Welcome to HYDERS PARK The Luxury Hotel, $comName, We are pleased to have you as our guest. Enjoy your stay \n";
        $msg .= "\n";
        $msg .= "We hope that your stay with us is both comfortable and memorable, \n";
        $msg .= "If you have any questions or concerns, please don't hesitate to reach out to our staff, \n";
        $msg .= "We are here to make your experience at $comName an exceptional one, \n";
        $msg .= "\n";

        $msg .= "Stay connected during your stay with our complimentary high-speed Wi-Fi, \n";
        // $msg .= "Enjoy seamless browsing and streaming in the comfort of your room, \n";
        // $msg .= "Keep in touch with loved ones, get some work done, \n";
        // $msg .= "all at lightning-fast speeds, \n";
        $msg .= "Enjoy your time in  $comName, \n";
        $msg .= "\n";

        $msg .= "USER NAME - HYDERSPARK, \n";
        $msg .= "PASSWORD - Park@1234, \n";
        $msg .= "Check Out  $checkOut, \n";

        $msg .= "Further information can be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        $msg .= "\n";
        $msg .= "\n";

        // $msg .= "About us.\n";
        $msg .= "Location $location\n";
        $msg .= "\n";
        $msg .= "More $video\n";

        $data = [
            'to' => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message' => $msg,
            'company' => $company ?? false,
            'instance_id' => $instance_id,
            'access_token' => $access_token,
        ];
        (new WhatsappController)->sentNotification($data);
    }

    public function storeDocument(Request $request)
    {
        $booking    = Booking::find($request->booking_id);
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

    public function updateByDrag(Request $request)
    {
        // return $request->only('check_in', 'check_out', 'id');
        return Booking::where('id', $request->id)->update($request->only('check_in', 'check_out'));
    }

    private function checkOutDate($date)
    {
        $date = date_create($date);
        date_modify($date, "-1 days");
        return date_format($date, "Y-m-d");
    }

    public function check_in_room_old(Request $request)
    {

        try {
            $booking_id               = $request->booking_id;
            $booking                  = Booking::find($booking_id);
            $rem                      = (float) $request->remaining_price - (float) $request->new_payment;
            $booking->remaining_price = $rem;
            $booking->grand_remaining_price = $rem;
            $booking->check_in_price  = $request->new_payment;
            $booking->payment_mode_id = $request->payment_mode_id;
            $booking->booking_status  = 2;

            $checkedIn = $booking->save();

            if ($checkedIn) {

                $transactionData = [
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id ?? '',
                    'date' => now(),
                    'company_id' => $booking->company_id ?? '',
                    'payment_method_id' => $booking->payment_mode_id,
                    'desc' => 'check in payment',
                ];

                $payment = new TransactionController();
                if ($request->new_payment && $request->new_payment > 0) {
                    $payment->store($transactionData, $request->new_payment, 'credit');

                    $paymentsData = [
                        'booking_id'   => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description'  => 'check in payment',
                        'amount'       => $request->new_payment,
                        'company_id'   => $booking->company_id,
                        'type'         => 'room',
                        'room'         => $booking->rooms,
                    ];

                    $payment =  Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int)$payment->amount - (int)$request->new_payment;
                        $payment->save();
                    }

                    $payment = new PaymentController();
                    $payment->store($paymentsData);


                    $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
                    $cityLedger =  Agent::whereBookingId($booking->id)->where('company_id', $booking->company_id)->first();

                    if ($cityLedger) {
                        $cityLedger->agent_paid_amount = $totCredit;
                        $cityLedger->save();
                    }
                } else {
                    $paymentsData = [
                        'booking_id'   => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description'  => 'check in payment',
                        'amount'       => $request->new_payment,
                        'company_id'   => $booking->company_id,
                        'type'         => 'room',
                        'room'         => $booking->rooms,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }

                BookedRoom::whereBookingId($booking_id)->update(['booking_status' => 2]);
                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }

            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);

            if ($booking->save()) {
                Room::where('id', $booking->room_id)->update(["status" => '2']);
                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_in_room(Request $request)
    {
        // return $request->all();
        try {
            $booking_id               = $request->booking_id;
            $booking                  = Booking::find($booking_id);
            $customer                  = Customer::find($request->customer_id);
            $rem                      = (float) $request->remaining_price - (float) $request->new_payment;
            $booking->remaining_price = $rem;
            $booking->grand_remaining_price = $rem;
            $booking->check_in_price  = $request->new_payment;
            $booking->payment_mode_id = $request->payment_mode_id;
            $booking->booking_status  = 2;
            $booking->id_card_no  = $request->id_card_no;
            $booking->expired  = $request->expired;
            $booking->id_card_type = IdCardType::find($request->id_card_type_id)->name ?? "";
            $customer->id_card_type_id = $request->id_card_type_id;
            $customer->id_card_no = $request->id_card_no;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $path = $file->storeAs('public/documents/booking', $fileName);
                Storage::copy($path, 'public/documents/customer/' . $fileName);
                $booking->document = $fileName;
                $customer->document = $fileName;
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $path = $file->storeAs('public/documents/customer/photo', $fileName);
                $customer->image = $fileName;
            }

            $checkedIn = $booking->save();

            if ($checkedIn) {
                $customer->save();
                $transactionData = [
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id ?? '',
                    'date' => now(),
                    'company_id' => $booking->company_id ?? '',
                    'payment_method_id' => $booking->payment_mode_id,
                    'desc' => 'check in payment',
                ];

                $payment = new TransactionController();
                if ($request->new_payment && $request->new_payment > 0) {
                    $payment->store($transactionData, $request->new_payment, 'credit');

                    $paymentsData = [
                        'booking_id'   => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description'  => 'check in payment',
                        'amount'       => $request->new_payment,
                        'company_id'   => $booking->company_id,
                        'type'         => 'room',
                        'room'         => $booking->rooms,
                    ];

                    $payment =  Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int)$payment->amount - (int)$request->new_payment;
                        $payment->save();
                    }

                    $payment = new PaymentController();
                    $payment->store($paymentsData);


                    $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
                    $cityLedger =  Agent::whereBookingId($booking->id)->where('company_id', $booking->company_id)->first();

                    if ($cityLedger) {
                        $cityLedger->agent_paid_amount = $totCredit;
                        $cityLedger->save();
                    }
                } else {
                    $paymentsData = [
                        'booking_id'   => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description'  => 'check in payment',
                        'amount'       => $request->new_payment,
                        'company_id'   => $booking->company_id,
                        'type'         => 'room',
                        'room'         => $booking->rooms,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }

                BookedRoom::whereBookingId($booking_id)->update(['booking_status' => 2]);



                $customer = Customer::find($booking->customer_id);
                $this->checkInNotification($booking,  $customer);

                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }

            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);

            if ($booking->save()) {
                Room::where('id', $booking->room_id)->update(["status" => '2']);
                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_out_room(Request $request)
    {
        try {
            $booking_id = $request->booking_id;
            $booking    = Booking::where('company_id', $request->company_id)->find($booking_id);

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $booking->payment_mode_id,
                'desc' => 'paid by city ledger',
            ];

            $trans = new TransactionController();
            if ($request->full_payment > 0) {
                $trans->store($transactionData, $request->full_payment, 'credit');
                // (new TransactionController)->store($transactionData, $request->full_payment, 'credit');
            }
            $booking = Booking::find($booking_id);
            if ($booking) {
                if ($booking->balance > 0) {
                    $booking->payment_status  = 0;
                    $booking->remaining_price       =  (int)$booking->remaining_price - (int)$request->full_payment;
                    $booking->grand_remaining_price = (int)$booking->remaining_price + (int)$booking->total_posting_amount;;

                    $paymentsData = [
                        'booking_id'     => $booking_id,
                        'payment_mode'   => $request->payment_mode_id,
                        'description'    =>  'checkout payment',
                        'amount'         => $request->full_payment,
                        'type'           => 'customer',
                        'room'           => $booking->rooms,
                        'company_id'     => $booking->company_id,
                        'is_city_ledger' => 0,
                        'created_at'     => now(),
                    ];
                    $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $booking->balance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                } else {
                    $booking->payment_status  = 1;
                    $booking->full_payment    = $booking->paid_amounts;
                    $booking->remaining_price       = 0;
                    $booking->grand_remaining_price = 0;
                    $booking->total_posting_amount  = 0;

                    $paymentsData = [
                        'booking_id'     => $booking_id,
                        'payment_mode'   => $request->payment_mode_id,
                        'description'    =>  'checkout payment',
                        'amount'         => $request->full_payment,
                        'type'           => 'customer',
                        'room'           => $booking->rooms,
                        'company_id'     => $booking->company_id,
                        'is_city_ledger' => 0,
                        'created_at'     => now(),
                    ];
                    $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $booking->balance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }
                $booking->booking_status  = 3;
                $booking->save();

                // if ($request->isPrintInvoice) {
                //     $inv = new InvoiceController;
                //     $inv->printInvoice($booking_id);
                // }
                return response()->json(['bookingId' => $booking_id, 'message' => 'Successfully Paid', 'status' => true]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_out_room1(Request $request)
    {
        $booking_id = $request->booking_id;
        $booking    = Booking::find($booking_id);
        $isFullPayment = false;
        if ($booking->grand_remaining_price <= $request->full_payment) {
            $isFullPayment = true;
            $booking->full_payment          = $booking->grand_remaining_price;
            $booking->remaining_price       = 0;
            $booking->grand_remaining_price = 0;
            $booking->total_posting_amount  = 0;
            $booking->payment_status = 1;
        } else {
            $booking->remaining_price = ((int) $booking->total_price - (int) $request->full_payment);
            $booking->grand_remaining_price =  ((int) $booking->remaining_price + (int) $booking->total_posting_amount);
            $booking->payment_status = 0;
        }
        $booking->payment_mode_id = $request->payment_mode_id;
        $booking->check_out_price  = $request->full_payment ?? 0;
        $booking->booking_status  = 3;

        if ($booking->save()) {
            $transactionData = [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id ?? '',
                'date' => now(),
                'company_id' => $booking->company_id ?? '',
                'payment_method_id' => $booking->payment_mode_id,
            ];

            $payment = new TransactionController();
            if ($request->full_payment && $request->full_payment > 0) {
                $payment->store($transactionData, $request->full_payment, 'credit');
            }

            // $totBalance = Transaction::whereBookingId($booking_id)->where('company_id', $booking->company_id)->orderBy('id', 'desc')->first();
            // $totRoomAmount = Transaction::whereBookingId($booking_id)->where('company_id', $booking->company_id)->orderBy('id', 'asc')->first();
            // $totCredit = Transaction::whereBookingId($booking_id)->where('company_id', $booking->company_id)->sum('credit');
            // return
            //     [
            //         $totRoomAmount,
            //         $totBalance,
            //         $totCredit,
            //     ];

            $paymentsData = [
                'booking_id'   => $booking_id,
                'payment_mode' => $request->payment_mode_id,
                'description'  => 'advance payment',
                'amount'       => $request->full_payment,
                'company_id'   => $booking->company_id,
                'type'         => 'room',
                'room'         => $booking->rooms,
            ];

            $payment =  Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            if ($payment) {
                $payment->amount = (int)$payment->amount - (int)$request->full_payment;
                $payment->save();
            }
            $payment = new PaymentController();
            $payment->store($paymentsData);



            $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
            $cityLedger =  Agent::whereBookingId($booking->id)->where('company_id', $booking->company_id)->first();
            if ($cityLedger) {
                $cityLedger->agent_paid_amount = $totCredit;
                $cityLedger->save();
            }

            return;

            if (!$isFullPayment) {
                $agentsData = [
                    'booking_id'   => $booking->id,
                    'customer_id'  => $booking->customer_id ?? '',
                    'type'         => 'Customer' ?? '',
                    'source'       => $booking->source,
                    'reference_no' => $booking->reference_no ?? '',
                    'amount'       => $booking->remaining_price ?? '',
                    'booking_date' => date('Y-m-d', strtotime($booking->created_at)) ?? '',
                    'company_id'   => $booking->company_id ?? '',
                ];

                $foundAgent = Agent::where('booking_id', $booking_id)->first();
                $payment = new AgentsController();
                if ($foundAgent) {
                    $payment->update($agentsData, $foundAgent);
                } else {
                    $payment->store($agentsData);
                }

                $paymentsData = [
                    'booking_id'   => $booking_id,
                    'payment_mode' => 7,
                    'description'  => 'payment pending',
                    'amount'       =>  $booking->remaining_price,
                    'type'         => 'room',
                    'room'         => $booking->rooms,
                    'company_id'   => $booking->company_id,
                    'is_city_ledger'   => $isFullPayment  ?  0 : 1,
                ];

                $found = Payment::where('booking_id', $booking_id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                $payment = new PaymentController();


                if ($found) {
                    $payment->update($paymentsData, $found);
                } else {
                    $payment->store($paymentsData);
                }
            } else {

                $paymentsData = [
                    'booking_id'   => $booking_id,
                    'payment_mode' => $request->payment_mode_id,
                    'description'  => 'check out full payment',
                    'amount'       =>  $request->full_payment,
                    'type'         => 'room',
                    'room'         => $booking->rooms,
                    'company_id'   => $booking->company_id,
                    'is_city_ledger'   => 0,
                ];

                $found = Payment::where('booking_id', $booking_id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                $payment = new PaymentController();


                if ($found) {
                    $payment->update($paymentsData, $found);
                } else {
                    $payment->store($paymentsData);
                }
            }

            BookedRoom::whereBookingId($booking_id)->update(['booking_status' => 3]);
            return response()->json(['data' => $booking_id, 'message' => 'Successfully check Out', 'status' => true]);
        }

        return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    }

    public function payingAdvance(Request $request)
    {
        try {
            $booking_id               = $request->booking_id;
            $booking                  = Booking::find($booking_id);
            $rem                      = (int) $request->remaining_price - (int) $request->new_advance;
            $booking->remaining_price = $rem;
            $booking->grand_remaining_price = (int)$rem + (int)$booking->total_posting_amount;
            $booking->advance_price   = (int)$booking->advance_price + (int)$request->new_advance;
            $booking->payment_mode_id = $request->payment_mode_id;

            if ($booking->save()) {

                $transactionData = [
                    'booking_id' => $booking_id,
                    'customer_id' => $booking->customer_id ?? '',
                    'date' => now(),
                    'company_id' => $booking->company_id ?? '',
                    'payment_method_id' => $request->payment_mode_id,
                    'desc' => 'advance payment',
                ];

                $payment = new TransactionController();
                if ($request->new_advance && $request->new_advance > 0) {
                    $payment->store($transactionData, $request->new_advance, 'credit');

                    $paymentsData = [
                        'booking_id'   => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description'  => 'advance payment',
                        'amount'       => $request->new_advance,
                        'company_id'   => $booking->company_id,
                        'type'         => 'room',
                        'room'         => $booking->rooms,
                    ];

                    $payment =  Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int)$payment->amount - (int)$request->new_advance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);

                    $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
                    $cityLedger =  Agent::whereBookingId($booking->id)->where('company_id', $booking->company_id)->first();
                    if ($cityLedger) {
                        $cityLedger->agent_paid_amount = $totCredit;
                        $cityLedger->save();
                    }
                }

                return response()->json(['data' => '', 'message' => 'Payment Successfully', 'status' => true]);
            }
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function payingAmount(Request $request)
    {
        try {
            $booking_id               = $request->booking_id;
            $booking                  = Booking::find($booking_id);
            $rem                      = (int) $request->remaining_price - (int) $request->new_advance;
            $booking->remaining_price = $rem;
            $booking->grand_remaining_price = (int)$rem + (int)$booking->total_posting_amount;
            $booking->advance_price   = $request->new_advance;
            $booking->payment_mode_id = $request->payment_mode_id;

            if ($booking->save()) {

                $transactionData = [
                    'booking_id' => $booking_id,
                    'customer_id' => $booking->customer_id ?? '',
                    'date' => now(),
                    'company_id' => $booking->company_id ?? '',
                    'payment_method_id' => $request->payment_mode_id,
                ];

                $payment = new TransactionController();
                if ($request->new_advance && $request->new_advance > 0) {
                    $payment->store($transactionData, $request->new_advance, 'credit');

                    $paymentsData = [
                        'booking_id'   => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description'  => 'advance payment',
                        'amount'       => $request->new_advance,
                        'company_id'   => $booking->company_id,
                        'type'         => 'room',
                        'room'         => $booking->rooms,
                    ];

                    $payment =  Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int)$payment->amount - (int)$request->new_advance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);

                    $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
                    $cityLedger =  Agent::whereBookingId($booking->id)->where('company_id', $booking->company_id)->first();
                    if ($cityLedger) {
                        $cityLedger->agent_paid_amount = $totCredit;
                        $cityLedger->save();
                    }
                }

                return response()->json(['bookingId' => $booking->id, 'message' => 'Payment Successfully', 'status' => true]);
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
        return BookedRoom::whereHas('booking', function ($q) use ($request) {
            $q->where('booking_status', '!=', 0);
            $q->where('company_id', $request->company_id);
        })->get(['id', 'room_id', 'booking_id', 'customer_id', 'check_in as start', 'check_out']);
    }

    public function events_list1(Request $request)
    {
        return Booking::where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }

    public function get_booking(Request $request)
    {
        $bookedRoom                     = BookedRoom::with(['booking', 'customer'])->where('company_id', $request->company_id)->findOrFail($request->id);
        $bookedRoom->booking->room_id   = $bookedRoom->room_id;
        $bookedRoom->booking->room_no   = $bookedRoom->room_no;
        $bookedRoom->booking->room_type = $bookedRoom->room_type;
        $bookedRoom->booking->contact_no = $bookedRoom->customer->contact_no;
        return  $bookedRoom->booking;
        // return response()->json(['booking' => $bookedRoom->booking, 'status' => true]);
    }

    public function cancelRoom(Request $request, $id)
    {
        try {
            $model = BookedRoom::find($id)
                ->makeHidden((new BookedRoom)->getCustomAppends());
            $numberOfRooms = BookedRoom::where('booking_id', $model->booking_id)->count();

            $bookedRoom = $model;
            if ($bookedRoom) {
                $bookedRoom->reason    = $request->reason;
                $bookedRoom->cancel_by = $request->cancel_by;

                $arr = $bookedRoom->toArray();
                $cancel = CancelRoom::create($arr);
                if ($cancel) {
                    $numberOfRooms == 1 ? Booking::where('id', $model->booking_id)->update(['booking_status' => -1]) : null;
                    $model->delete();
                }
            }

            return response()->json(['data' => '', 'message' => 'Successfully canceled', 'status' => true]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setAvailable($id)
    {
        try {
            $booking = Booking::find($id);
            $booking->update([
                'booking_status' => 0,
            ]);
            if ($booking) {
                BookedRoom::whereBookingId($booking->id)->update(['booking_status' => 0]);
                return $this->response('Now room available.', null, true);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setMaintenance($id)
    {
        try {
            $booking = Booking::find($id);
            $booking->update([
                'booking_status' => 4,
            ]);
            if ($booking) {
                BookedRoom::whereBookingId($booking->id)->update(['booking_status' => 4]);
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

    private function getBookedRoomsFromBookingId($id)
    {
        $bookedRooms = BookedRoom::whereBookingId($id)->pluck('room_no')->toArray();
        $string = implode(', ', $bookedRooms);
        return $string;
    }

    public function changeRoomByDrag(Request $request)
    {
        try {
            $bookedRoom = BookedRoom::where('company_id', $request->company_id)->find($request->eventId);
            $room   = Room::where('company_id', $request->company_id)->whereRoomNo($request->roomId)->first();

            $bookedRoom->room_id = $room->id;
            $bookedRoom->room_no = $room->room_no;

            if ($bookedRoom->save()) {
                $rooms = $this->getBookedRoomsFromBookingId($bookedRoom->booking_id);
                $bookedRoom->booking()->update(['rooms' => $rooms]);
                return $this->response('Room changed Successfully.', null, true);
            }

            return $this->response('DataBase Error in status change', null, true);
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
            $roomId = Room::whereRoomNo($request->roomId)->first()->id ?? '';
            $booked = $booked->update([
                'check_in'  => $request->start,
                'check_out' => $request->end,
                'room_id'   => $roomId,
            ]);

            if (now() >= $request->start) {
                $room = new RoomController();
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

    public function reservationList(Request $request)
    {
        $model = Booking::query()
            ->latest()
            ->filter(request('search'));

        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name,document',
            ])
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', -1)
            // ->where('booking_status', '!=', 0)
            ->paginate($request->per_page ?? 20);
    }

    public function reservationListForDash(Request $request)
    {
        $model = Booking::query()
            ->latest();
        $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name,document',
            ]);

        $model->where('company_id', $request->company_id);

        if ($request->filled('status') && $request->status == 1) {
            $model->where('booking_status', $request->status);
            $model->WhereDate('check_in',  $request->date);
        } else if ($request->filled('status') && $request->status == 2) {
            $model->where('booking_status', $request->status);
            $model->whereDate('check_in', '<=',  $request->date);
        } else if ($request->filled('status') && $request->status == 3) {
            $model->where('booking_status', $request->status);
            $model->WhereDate('check_out',  $request->date);
        }

        $model->where('booking_status', '!=', 0);
        $model->where('booking_status', '<=', 2);
        return   $model->paginate($request->per_page ?? 20);
    }

    public function getBookedRooms(Request $request)
    {
        return BookedRoom::whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', 0)
                ->where('booking_status', '<=', 2);
        })
            ->with('roomType')
            ->withOut('postings')
            ->get(['id', 'room_id', 'booking_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }
}