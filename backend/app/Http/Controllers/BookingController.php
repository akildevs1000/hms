<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingRequest;
use App\Http\Requests\Booking\DocumentRequest;
use App\Models\Agent;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Customer;
use App\Models\Food;
use App\Models\IdCardType;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Storage;

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

    public function document_validate(DocumentRequest $request)
    {
        return $this->response('Document validated.', null, true);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $customer_id            = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            $booking                = $this->storeBooking($request);
            if ($booking) {
                $this->storeBookedRooms($request, $booking);
                return response()->json(['data' => $booking->id, 'status' => true]);
            }
        });
    }

    public function storeBooking($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data                          = [];
                $data                          = $request->only(Booking::bookingAttributes());
                $data['booking_date']          = now();
                $data['payment_status']        = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
                $data['remaining_price']       = (float) $request->total_price - (float) $request->advance_price;
                $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
                $data['reservation_no']        = $this->getReservationNumber($data);

                $booked = Booking::create($data);

                if ($booked) {

                    Food::create([
                        'booking_id' => $booked->id,
                        'breakfast'  => $request->json('qty_breakfast'),
                        'lunch'      => $request->json('qty_lunch'),
                        'dinner'     => $request->json('qty_dinner'),
                    ]);

                    $transactionData = [
                        'booking_id'        => $booked->id,
                        'customer_id'       => $booked->customer_id ?? '',
                        'date'              => now(),
                        'company_id'        => $request->company_id ?? '',
                        'payment_method_id' => $booked->payment_mode_id,
                        'desc'              => 'rooms booking amount',
                        'reference_number'  => $request->reference_number,
                    ];

                    //Transaction
                    $payment = new TransactionController();
                    $payment->store($transactionData, $request->total_price, 'debit');

                    if ($request->advance_price && $request->advance_price > 0) {
                        $transactionData['desc'] = 'advance payment';
                        $payment->store($transactionData, $request->advance_price, 'credit');
                    }
                    //End Transaction
                    if ((float) $booked->advance_price == 0) {

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
                                'booking_id'     => $booked->id,
                                'payment_mode'   => 7,
                                'description'    => 'booked from ' . $booked->source,
                                'amount'         => $booked->remaining_price,
                                'type'           => 'room',
                                'room'           => $booked->rooms,
                                'company_id'     => $request->company_id,
                                'is_city_ledger' => 1,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        } else {
                            $paymentsData = [
                                'booking_id'     => $booked->id,
                                'payment_mode'   => 7,
                                'description'    => 'booked from ' . $booked->source,
                                'amount'         => $booked->remaining_price,
                                'type'           => 'room',
                                'room'           => $booked->rooms,
                                'company_id'     => $request->company_id,
                                'is_city_ledger' => 1,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        }
                    } else {

                        if ($request->total_price >= $request->advance_price) {

                            $paymentsData = [
                                'booking_id'     => $booked->id,
                                'payment_mode'   => $booked->payment_mode_id,
                                'description'    => 'advance payment',
                                'amount'         => $booked->advance_price,
                                'type'           => 'room',
                                'room'           => $booked->rooms,
                                'company_id'     => $request->company_id,
                                'is_city_ledger' => 0,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        }

                        $paymentsData = [
                            'booking_id'     => $booked->id,
                            'payment_mode'   => 7,
                            'description'    => 'pending payment',
                            'amount'         => $booked->remaining_price,
                            'type'           => 'room',
                            'room'           => $booked->rooms,
                            'company_id'     => $request->company_id,
                            'is_city_ledger' => 1,
                        ];
                        $payment = new PaymentController();
                        $payment->store($paymentsData);

                        $agentsData = [
                            'booking_id'        => $booked->id,
                            'customer_id'       => $booked->customer_id ?? '',
                            'type'              => 'Customer' ?? '',
                            'source'            => $booked->source,
                            'reference_no'      => $booked->reference_no ?? '',
                            'amount'            => $booked->total_price ?? '',
                            'agent_paid_amount' => $booked->advance_price ?? '',
                            'booking_date'      => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                            'company_id'        => $request->company_id ?? '',
                        ];
                        $payment = new AgentsController();
                        $payment->store($agentsData);
                    }

                    if ($request->gst_number) {
                        (new TaxableController)->storeTaxableInvoice($booked);
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

    public function getReservationNumber($data)
    {
        $company_id     = $data['company_id'];
        $starting_value = 00001;
        $model          = Booking::query();

        // (int) $counter = $model->where('company_id', $company_id)->latest('reservation_no')->value('reservation_no') ?? $starting_value;
        (int) $counter = $model->where('company_id', $company_id)->orderBy('id', 'desc')->first()->reservation_no ?? $starting_value;
        $exist         = $model->where('company_id', $company_id)->where('reservation_no', $counter)->exists();

        if ($exist) {
            return (int) ++$counter;
        } else {
            return $starting_value;
        }
    }

    public function storeBooking_old($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data                          = [];
                $data                          = $request->only(Booking::bookingAttributes());
                $data['booking_date']          = now();
                $data['payment_status']        = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
                $data['remaining_price']       = (float) $request->total_price - (float) $request->advance_price;
                $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
                $data['reservation_no']        = $this->getReservationNumber($data);

                $booked = Booking::create($data);

                if ($booked) {

                    Food::create([
                        'booking_id' => $booked->id,
                        'breakfast'  => $request->json('qty_breakfast'),
                        'lunch'      => $request->json('qty_lunch'),
                        'dinner'     => $request->json('qty_dinner'),
                    ]);

                    $transactionData = [
                        'booking_id'        => $booked->id,
                        'customer_id'       => $booked->customer_id ?? '',
                        'date'              => now(),
                        'company_id'        => $request->company_id ?? '',
                        'payment_method_id' => $booked->payment_mode_id,
                        'desc'              => 'rooms booking amount',
                        'reference_number'  => $request->reference_number,
                    ];

                    //Transaction
                    $payment = new TransactionController();
                    $payment->store($transactionData, $request->total_price, 'debit');

                    if ($request->advance_price && $request->advance_price > 0) {
                        $transactionData['desc'] = 'advance payment';
                        $payment->store($transactionData, $request->advance_price, 'credit');
                    }
                    //End Transaction
                    if ((float) $booked->advance_price == 0) {

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
                                'booking_id'     => $booked->id,
                                'payment_mode'   => 7,
                                'description'    => 'booked from ' . $booked->source,
                                'amount'         => $booked->remaining_price,
                                'type'           => 'room',
                                'room'           => $booked->rooms,
                                'company_id'     => $request->company_id,
                                'is_city_ledger' => 1,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        } else {
                            $paymentsData = [
                                'booking_id'     => $booked->id,
                                'payment_mode'   => 7,
                                'description'    => 'booked from ' . $booked->source,
                                'amount'         => $booked->remaining_price,
                                'type'           => 'room',
                                'room'           => $booked->rooms,
                                'company_id'     => $request->company_id,
                                'is_city_ledger' => 1,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        }
                    } else {

                        if ($request->total_price >= $request->advance_price) {

                            $paymentsData = [
                                'booking_id'     => $booked->id,
                                'payment_mode'   => $booked->payment_mode_id,
                                'description'    => 'advance payment',
                                'amount'         => $booked->advance_price,
                                'type'           => 'room',
                                'room'           => $booked->rooms,
                                'company_id'     => $request->company_id,
                                'is_city_ledger' => 0,
                            ];
                            $payment = new PaymentController();
                            $payment->store($paymentsData);
                        }

                        $paymentsData = [
                            'booking_id'     => $booked->id,
                            'payment_mode'   => 7,
                            'description'    => 'pending payment',
                            'amount'         => $booked->remaining_price,
                            'type'           => 'room',
                            'room'           => $booked->rooms,
                            'company_id'     => $request->company_id,
                            'is_city_ledger' => 1,
                        ];
                        $payment = new PaymentController();
                        $payment->store($paymentsData);

                        $agentsData = [
                            'booking_id'        => $booked->id,
                            'customer_id'       => $booked->customer_id ?? '',
                            'type'              => 'Customer' ?? '',
                            'source'            => $booked->source,
                            'reference_no'      => $booked->reference_no ?? '',
                            'amount'            => $booked->total_price ?? '',
                            'agent_paid_amount' => $booked->advance_price ?? '',
                            'booking_date'      => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                            'company_id'        => $request->company_id ?? '',
                        ];
                        $payment = new AgentsController();
                        $payment->store($agentsData);
                    }

                    if ($request->gst_number) {
                        (new TaxableController)->storeTaxableInvoice($booked);
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
            $rooms = $request->only('selectedRooms');
            foreach ($rooms['selectedRooms'] as $room) {
                $room['booking_id']     = $booking->id;
                $room['customer_id']    = $booking->customer_id;
                $room['booking_status'] = $booking->booking_status;
                $bookedRoomId           = BookedRoom::create($room);
                $orderRooms             = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));
                $period                 = CarbonPeriod::create($room['check_in'], $this->checkOutDate($room['check_out']));
                foreach ($period as $date) {
                    $orderRooms['date']           = $date->format('Y-m-d');
                    $orderRooms['booked_room_id'] = $bookedRoomId->id;
                    OrderRoom::create($orderRooms);
                }
            }

            if (app()->isProduction()) {
                $customer = Customer::find($booking->customer_id);
                $this->whatsappNotification($booking, $rooms['selectedRooms'], $customer, 'booking');
            }

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
        $instance_id     = "";
        $access_token    = "";
        $msg             = "";
        $numberOfRooms   = count($rooms);
        $customerName    = ucfirst($customer['first_name']) ?? 'Guest';
        $title           = ucfirst($customer['title']) ?? ' ';
        $checkIn         = date('d-M-y', strtotime($booking->check_in));
        $checkOut        = date('d-M-y H:i', strtotime($booking->check_out));
        $days            = $booking->total_days;
        $bookedRooms     = $booking->rooms;
        $totalAmount     = $booking->total_price;
        $advanceAmount   = $booking->advance_price;
        $remainingAmount = $booking->remaining_price;
        $company_id      = $booking->company_id;

        if ($company_id == 1) {
            $location     = "  https://goo.gl/maps/gA9h3YxGTwLaRETG6";
            $company      = 1;
            $instance_id  = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $comName      = "Thanjavur";
        } else if ($company_id == 2) {
            $location     = "  https://goo.gl/maps/bNznm2Z4pbxo2ZJw9";
            $company      = 2;
            $instance_id  = "KODAI_INSTANCE_ID";
            $access_token = "KODAI_ACCESS_TOKEN";
            $comName      = "Kodaikanal";
        }

        $msg .= "Dear $title $customerName, \n";
        $msg .= "Welcome to HYDERS PARK The Luxury Hotel, $comName, \n";
        $msg .= "Your booking reference number, $booking->id, \n";
        $msg .= "Number of Rooms, $numberOfRooms, \n";
        $msg .= "from, $checkIn to $checkOut, \n";
        $msg .= "Your total bill is ₹$totalAmount \n";
        $msg .= "You paid advance ₹$advanceAmount \n";
        $msg .= "Your remaining amount is ₹$remainingAmount \n";

        if ($advanceAmount <= 0) {
            $msg .= "Please pay Advance to confirm your booking  .\n";
            // $msg .= "You must pay an advance within 48 hours to confirm your booking.\n";
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
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
        ];
        (new WhatsappController)->sentNotification($data);
    }

    private function checkInNotification($booking, $customer)
    {
        $instance_id  = "";
        $access_token = "";
        $comName      = "";
        $location     = "";
        $video        = "";
        $msg          = "";
        $customerName = ucfirst($customer['first_name']) ?? 'Guest';
        $checkOut     = date('d-M-y H:i', strtotime($booking->check_out));
        $company_id   = $booking->company_id;

        if ($company_id == 1) {
            $location     = "  https://goo.gl/maps/gA9h3YxGTwLaRETG6";
            $company      = 1;
            $instance_id  = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $video        = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
            $comName      = "Thanjavur";
        } else if ($company_id == 2) {
            $location     = "  https://goo.gl/maps/bNznm2Z4pbxo2ZJw9";
            $company      = 2;
            $instance_id  = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $comName      = "Kodaikanal";
            $video        = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
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
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
        ];
        (new WhatsappController)->sentNotification($data);
    }

    private function checkOutNotification($booking, $customer)
    {
        $instance_id  = "";
        $access_token = "";
        $comName      = "";
        $location     = "";
        $review       = "";
        $video        = "";
        $msg          = "";
        $title        = ucfirst($customer['title']) ?? 'Mr';
        $customerName = ucfirst($customer['first_name']) ?? 'Guest';
        $checkOut     = date('d-M-y H:i', strtotime($booking->check_out));
        $company_id   = $booking->company_id;

        if ($company_id == 1) {
            $location     = "  https://goo.gl/maps/gA9h3YxGTwLaRETG6";
            $company      = 1;
            $instance_id  = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $video        = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
            $comName      = "Thanjavur";
        } else if ($company_id == 2) {
            $location     = "  https://goo.gl/maps/bNznm2Z4pbxo2ZJw9";
            $company      = 2;
            $instance_id  = "THANJ_INSTANCE_ID";
            $access_token = "THANJ_ACCESS_TOKEN";
            $comName      = "Kodaikanal";
            $video        = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
            $review       = "https://search.google.com/local/writereview?placeid=ChIJP4ZnsL1nBzsRNgn1M2X8Gd4";
        }

        $msg .= "Dear $title $customerName, \n";

        $msg .= "Thank you for your stay \n";
        $msg .= "\n";
        $msg .= "We hope that your experience with us was great, \n";
        $msg .= "We had a great time serving you, \n";
        $msg .= "we look forward to having you back with us soon, \n";
        $msg .= "Safe Travels, \n";
        $msg .= "\n";

        $msg .= "Further information can be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        $msg .= "\n";
        $msg .= "$review";

        $msg .= "Location $location\n";
        $msg .= "\n";
        $msg .= "More $video\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
        ];
        // dd($data);
        (new WhatsappController)->sentNotification($data);
    }

    public function storeDocument(Request $request)
    {
        $booking  = Booking::find($request->booking_id);
        $customer = Customer::find($booking->customer_id);
        if ($request->hasFile('document')) {
            $file     = $request->file('document');
            $ext      = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path     = $file->storeAs('public/documents/booking', $fileName);
            Storage::copy($path, 'public/documents/customer/' . $fileName);
            $booking->document  = $fileName;
            $customer->document = $fileName;
        } else {
            $booking->document = $customer->document_name ?? null;
        }

        if ($request->hasFile('image')) {
            $file            = $request->file('image');
            $ext             = $file->getClientOriginalExtension();
            $fileName        = time() . '.' . $ext;
            $path            = $file->storeAs('public/documents/customer/photo', $fileName);
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

    // public function checkOutDate($date)
    // {
    //     $date = date_create($date);
    //     date_modify($date, "-1 days");
    //     return date_format($date, "Y-m-d");
    // }

    // public function check_in_room(Request $request)
    // {
    //     try {
    //         $booking_id              = $request->booking_id;
    //         $booking                 = Booking::find($booking_id);
    //         $customer                = Customer::find($request->customer_id);
    //         $booking->check_in_price = $request->new_payment ?? 0;
    //         $booking->booking_status = 2;
    //         $booking->id_card_no     = $request->id_card_no;
    //         $booking->expired        = $request->expired;
    //         $booking->id_card_type   = IdCardType::find($request->id_card_type_id)->name ?? "";

    //         if ($request->hasFile('document')) {
    //             $file     = $request->file('document');
    //             $ext      = $file->getClientOriginalExtension();
    //             $fileName = time() . '.' . $ext;
    //             $path     = $file->storeAs('public/documents/booking', $fileName);
    //             Storage::copy($path, 'public/documents/customer/' . $fileName);
    //             $booking->document  = $fileName;
    //             $customer->document = $fileName;
    //         }

    //         if ($request->hasFile('image')) {
    //             $file            = $request->file('image');
    //             $ext             = $file->getClientOriginalExtension();
    //             $fileName        = time() . '.' . $ext;
    //             $path            = $file->storeAs('public/documents/customer/photo', $fileName);
    //             $customer->image = $fileName;
    //         }
    //         $checkedIn = $booking->save();

    //         if ($checkedIn) {
    //             $customer->save();

    //             $this->updateTransaction($booking, $request, 'check in payment');
    //             $this->updatePayment($booking, $request);

    //             BookedRoom::whereBookingId($booking_id)->update(['booking_status' => 2]);

    //             if (app()->isProduction()) {
    //                 $customer = Customer::find($booking->customer_id);
    //                 $this->checkInNotification($booking, $customer);
    //             }
    //             $customerData       = $request->only(Customer::customerAttributes());
    //             $customerData['id'] = $request->customer_id;
    //             $this->customerUpdateById($customerData);
    //             return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
    //         }

    //         return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
    //     } catch (\Throwable $th) {
    //         return response()->json(['data' => $th, 'message' => 'Unsuccessfully update', 'status' => false]);
    //         // throw $th;
    //     }
    // }

    private function updateTransaction($booking, $request, $desc = "", $mode, $amt)
    {
        $transactionData = [
            'booking_id'        => $booking->id,
            'customer_id'       => $booking->customer_id ?? '',
            'date'              => now(),
            'company_id'        => $booking->company_id ?? '',
            'payment_method_id' => $booking->payment_mode_id,
            'desc'              => $desc,
            'reference_number'  => $request->reference_number,
        ];
        (new TransactionController)->store($transactionData, $amt, $mode);
        (new TransactionController)->updateBookingByTransactions($booking->id, 0);
    }

    private function updatePayment($booking, $request, $amt, $desc = "")
    {
        $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
        if ($payment) {
            $payment->amount = (float) $payment->amount - (float) $amt;
            $payment->save();
        }

        $paymentsData = [
            'booking_id'   => $booking->id,
            'payment_mode' => $request->payment_mode_id,
            'description'  => $desc,
            'amount'       => $amt,
            'company_id'   => $booking->company_id,
            'type'         => 'room',
            'room'         => $booking->rooms,
        ];

        $payment = new PaymentController();
        $payment->store($paymentsData);
    }

    public function customerUpdateById($customer)
    {
        try {
            $isExistCustomer = Customer::find($customer['id']);
            if ($isExistCustomer) {
                $isExistCustomer->update($customer);
            }
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function customerStore($customer)
    {
        try {
            $isExistCustomer = Customer::whereContactNo($customer['contact_no'])->whereCompanyId($customer['company_id'])->first();
            $id              = "";
            if ($isExistCustomer) {
                $id = $isExistCustomer->id;
                $isExistCustomer->update($customer);
            } else {
                $record = Customer::create($customer);
                $id     = $record->id;
            }
            return $id;
            return $this->response('Customer successfully added.', $id, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_in_room(Request $request)
    {
        try {
            $booking_id              = $request->booking_id;
            $booking                 = Booking::find($booking_id);
            $customer                = Customer::find($request->customer_id);
            $booking->check_in_price = $request->new_payment;
            $booking->booking_status = 2;
            $booking->id_card_no     = $request->id_card_no;
            $booking->expired        = $request->expired;
            $booking->id_card_type   = IdCardType::find($request->id_card_type_id)->name ?? "";

            if ($request->hasFile('document')) {
                $file     = $request->file('document');
                $ext      = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $path     = $file->storeAs('public/documents/booking', $fileName);
                Storage::copy($path, 'public/documents/customer/' . $fileName);
                $booking->document  = $fileName;
                $customer->document = $fileName;
            }

            if ($request->hasFile('image')) {
                $file            = $request->file('image');
                $ext             = $file->getClientOriginalExtension();
                $fileName        = time() . '.' . $ext;
                $path            = $file->storeAs('public/documents/customer/photo', $fileName);
                $customer->image = $fileName;
            }
            $checkedIn = $booking->save();
            if ($checkedIn) {
                $customer->save();
                $this->updateTransaction($booking, $request, 'check in payment', 'credit', $request->new_payment);
                $this->updatePayment($booking, $request, $request->new_payment, 'checkin payment');
                BookedRoom::whereBookingId($booking_id)->update(['booking_status' => 2]);
                if (app()->isProduction()) {
                    $customer = Customer::find($booking->customer_id);
                    $this->checkInNotification($booking, $customer);
                }
                $customerData       = $request->only(Customer::customerAttributes());
                $customerData['id'] = $request->customer_id;
                $this->customerUpdateById($customerData);
                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }

            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Throwable $th) {
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
            // throw $th;
        }
    }

    public function check_out_room(Request $request)
    {
        try {
            $booking_id = $request->booking_id;
            $booking    = Booking::where('company_id', $request->company_id)->find($booking_id);
            $customer   = Customer::find($booking->customer_id);
            if ($request->discount > 0) {
                $this->updateTransaction($booking, $request, 'discount', 'debit', -abs($request->discount));
            }

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $booking->payment_mode_id,
                'desc'              => 'check out payment',
            ];

            $trans = new TransactionController();
            if ($request->full_payment > 0) {
                $trans->store($transactionData, $request->full_payment, 'credit');
            }

            $booking = Booking::find($booking_id);
            if ($booking) {
                $customer = Customer::find($booking->customer_id);

                if ($booking->balance > 0) {
                    $booking->payment_status        = 0;
                    $booking->remaining_price       = (int) $booking->remaining_price - (int) $request->full_payment;
                    $booking->grand_remaining_price = (int) $booking->remaining_price + (int) $booking->total_posting_amount;

                    $paymentsData = [
                        'booking_id'     => $booking_id,
                        'payment_mode'   => $request->payment_mode_id,
                        'description'    => 'checkout payment',
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
                    $booking->payment_status        = 1;
                    $booking->full_payment          = $booking->paid_amounts;
                    $booking->remaining_price       = 0;
                    $booking->grand_remaining_price = 0;
                    $booking->total_posting_amount  = 0;

                    $paymentsData = [
                        'booking_id'     => $booking_id,
                        'payment_mode'   => $request->payment_mode_id,
                        'description'    => 'checkout payment',
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
                $booking->booking_status = 3;
                $booking->save();

                if (app()->isProduction()) {
                    $this->checkOutNotification($booking, $customer);
                }

                return response()->json(['bookingId' => $booking_id, 'message' => 'Successfully Paid', 'status' => true]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_out_room_old(Request $request)
    {
        try {

            $booking_id = $request->booking_id;
            $booking    = Booking::where('company_id', $request->company_id)->find($booking_id);

            $transactionDataForDiscount = [
                'booking_id'  => $booking->id,
                'customer_id' => $booking->customer_id ?? '',
                'date'        => now(),
                'company_id'  => $booking->company_id ?? '',
                'desc'        => 'discount',
            ];

            $trans = new TransactionController();
            if ($request->discount > 0) {
                $trans->store($transactionDataForDiscount, -abs($request->discount), 'debit');
            }

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $booking->payment_mode_id,
                'desc'              => 'paid by city ledger',
            ];

            $trans = new TransactionController();
            if ($request->full_payment > 0) {
                $trans->store($transactionData, $request->full_payment, 'credit');
            }
            return response()->json(['bookingId' => $booking_id, 'message' => 'Successfully Paid', 'status' => true]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function payingAdvance(Request $request)
    {
        try {
            $booking         = Booking::find($request->booking_id);
            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $request->payment_mode_id,
                'desc'              => 'advance payment',
                'reference_number'  => $request->reference_number,
            ];

            (new TransactionController)->store($transactionData, $request->new_advance, 'credit');
            (new TransactionController)->updateBookingByTransactions($booking->id, 0);
            $booking->advance_price = (int) $booking->advance_price + (int) $request->new_advance;
            $booking->save();
            $paymentsData = [
                'booking_id'   => $booking->id,
                'payment_mode' => $request->payment_mode_id,
                'description'  => 'advance payment',
                'amount'       => $request->new_advance,
                'company_id'   => $booking->company_id,
                'type'         => 'room',
                'room'         => $booking->rooms,
            ];

            $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            if ($payment) {
                $payment->amount = (int) $payment->amount - (int) $request->new_advance;
                $payment->save();
            }
            $payment = new PaymentController();
            $payment->store($paymentsData);

            return response()->json(['data' => '', 'message' => 'Payment Successfully', 'status' => true]);
        } catch (\Throwable $th) {
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
            // throw $th;
        }
    }

    public function payingAmount(Request $request)
    {
        try {
            $booking_id                     = $request->booking_id;
            $booking                        = Booking::find($booking_id);
            $rem                            = (int) $request->remaining_price - (int) $request->new_advance;
            $booking->remaining_price       = $rem;
            $booking->grand_remaining_price = (int) $rem + (int) $booking->total_posting_amount;
            $booking->advance_price         = $request->new_advance;
            $booking->payment_mode_id       = $request->payment_mode_id;

            if ($booking->save()) {

                $transactionData = [
                    'booking_id'        => $booking_id,
                    'customer_id'       => $booking->customer_id ?? '',
                    'date'              => now(),
                    'company_id'        => $booking->company_id ?? '',
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

                    $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $payment->amount - (int) $request->new_advance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);

                    $totCredit  = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
                    $cityLedger = Agent::whereBookingId($booking->id)->where('company_id', $booking->company_id)->first();
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

    public function events_list(Request $request)
    {
        return BookedRoom::whereHas('booking', function ($q) use ($request) {
            // $q->where('booking_status', '!=', 0);
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
        $bookedRoom                      = BookedRoom::with(['booking', 'customer'])->where('company_id', $request->company_id)->findOrFail($request->id);
        $bookedRoom->booking->room_id    = $bookedRoom->room_id;
        $bookedRoom->booking->room_no    = $bookedRoom->room_no;
        $bookedRoom->booking->room_type  = $bookedRoom->room_type;
        $bookedRoom->booking->contact_no = $bookedRoom->customer->contact_no;
        return $bookedRoom->booking;
        // return response()->json(['booking' => $bookedRoom->booking, 'status' => true]);
    }

    public function cancelRoom(Request $request, $id)
    {
        try {
            $model         = BookedRoom::find($id);
            $numberOfRooms = BookedRoom::where('booking_id', $model->booking_id)->count();

            $bookedRoom = $model;
            if ($bookedRoom) {
                $bookedRoom->reason    = $request->reason;
                $bookedRoom->cancel_by = $request->cancel_by;

                $arr    = $bookedRoom->toArray();
                $cancel = CancelRoom::create($arr);
                if ($cancel) {
                    $transactionData = [
                        'booking_id'        => $bookedRoom->booking_id,
                        'customer_id'       => $bookedRoom->customer_id ?? '',
                        'date'              => now(),
                        'company_id'        => $bookedRoom->company_id ?? '',
                        'payment_method_id' => 7,
                        'desc'              => "room $model->room_no canceled",
                    ];
                    (new TransactionController)->store($transactionData, -$model->grand_total, 'debit');
                    (new TransactionController)->updateBookingByTransactions($model->booking_id, -$model->grand_total);

                    $payment = Payment::whereBookingId($bookedRoom->booking_id)->where('company_id', $bookedRoom->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $payment->amount - (int) $model->grand_total;
                        $payment->save();
                    }

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
            $booking->update(['booking_status' => 0]);
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
            $booking->update(['booking_status' => 4]);
            if ($booking) {
                BookedRoom::whereBookingId($booking->id)->update(['booking_status' => 4]);
                return $this->response('Now room maintenance.', null, true);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function getBookedRoomsFromBookingId($id)
    {
        $bookedRooms = BookedRoom::whereBookingId($id)->pluck('room_no')->toArray();
        $string      = implode(', ', $bookedRooms);
        return $string;
    }

    private function getRoomTax($amount)
    {
        $temp                   = [];
        $per                    = $amount < 3000 ? 12 : 18;
        $tax                    = ($amount / 100) * $per;
        $temp['room_tax']       = $tax;
        $temp['total_with_tax'] = (float) $amount + (float) $tax;
        $temp['after_discount'] = $amount;
        $gst                    = floatval($tax) / 2;
        $temp['cgst']           = $gst;
        $temp['sgst']           = $gst;
        return $temp;
    }

    private function getRoomAmtWithTax($oldRoom, $newRoom, $request)
    {
        $afterDiscount       = (float) $newRoom->room_type->price - (float) $oldRoom->room_discount;
        $data                = $this->getRoomTax($afterDiscount);
        $data['total']       = (float) $data['total_with_tax'] + (float) $oldRoom->tot_adult_food + (float) $oldRoom->tot_child_food;
        $data['grand_total'] = (float) $data['total'] * $oldRoom->days;
        $data['price']       = $newRoom->price;
        $data['room_no']     = $newRoom->room_no;
        $data['room_id']     = $newRoom->id;
        $data['room_type']   = $newRoom->room_type->name ?? "";
        $data['check_in']    = date('Y-m-d', strtotime($request->start));
        $data['check_out']   = date('Y-m-d', strtotime($request->end));
        return array_merge($oldRoom->toArray(), $data);
    }

    public function changeRoomByDrag(Request $request)
    {
        try {
            $oldRoom = BookedRoom::without('postings', 'booking')->where('company_id', $request->company_id)->find($request->eventId);
            $bookingModel =  $this->gerBookingModel($oldRoom->booking_id);

            if ($bookingModel->booking_status == 0) {
                return $this->response('oops cant change already guest checkout.', null, true);
            }

            $newRoom       = Room::where('company_id', $request->company_id)->whereRoomNo($request->roomId)->first();
            $newUpdateRoom = $this->getRoomAmtWithTax($oldRoom, $newRoom, $request);
            $newUpdateRoom['check_out'];
            $extraAmt             = $newRoom->room_type->price - $oldRoom->price;
            $bookedRoomAttributes = array_intersect_key($newUpdateRoom, array_flip(BookedRoom::bookedRoomAttributes()));
            $transactionData      = [
                'booking_id'        => $oldRoom->booking_id,
                'customer_id'       => $oldRoom->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $oldRoom->company_id ?? '',
                'payment_method_id' => 7,
                'desc'              => "room/date change",
            ];
            (new TransactionController)->store($transactionData, $extraAmt, 'debit');
            $transactionSummary = (new TransactionController)->getTransactionSummaryByBookingId($oldRoom->booking_id);
            if ($oldRoom->update($bookedRoomAttributes)) {
                $oldRoom->booking->update([
                    'check_in'              => $newUpdateRoom['check_in'],
                    'check_out'             => $newUpdateRoom['check_out'],
                    'rooms'                 => $this->getBookedRoomsFromBookingId($oldRoom->booking_id),
                    'total_price'           => Booking::find($oldRoom->booking_id)->total_price + $extraAmt,
                    'grand_remaining_price' => $transactionSummary['balance'],
                    'balance'               => $transactionSummary['balance'],
                    'remaining_price'       => $transactionSummary['balance'],
                    'paid_amounts'          => $transactionSummary['sumCredit'],

                ]);
                BookedRoom::whereBookingId($oldRoom->booking_id)->update([
                    'check_in'  => date('Y-m-d 11:00', strtotime($newUpdateRoom['check_in'])),
                    'check_out' => date('Y-m-d 11:00', strtotime($newUpdateRoom['check_out'])),
                ]);
                $deleted =  OrderRoom::whereBookedRoomId($request->eventId)->delete();
                if ($deleted) {
                    $orderRooms             = array_intersect_key($bookedRoomAttributes, array_flip(OrderRoom::orderRoomAttributes()));
                    $period                 = CarbonPeriod::create($newUpdateRoom['check_in'], $this->checkOutDate($newUpdateRoom['check_out']));
                    foreach ($period as $date) {
                        $orderRooms['date']           = $date->format('Y-m-d');
                        $orderRooms['booked_room_id'] = $request->eventId;
                        $orderRooms['booking_id'] = $newUpdateRoom['booking_id'];
                        OrderRoom::create($orderRooms);
                    }
                }

                $paymentsData = [
                    'booking_id'   => $oldRoom->booking_id,
                    'payment_mode' => 7,
                    'description'  => 'room/date change',
                    'amount'       => $extraAmt,
                    'company_id'   => $oldRoom->company_id,
                    'type'         => 'room',
                    'room'         => $oldRoom->booking->rooms ?? "",
                ];

                $payment = Payment::whereBookingId($oldRoom->booking_id)->where('company_id', $oldRoom->company_id)->where('is_city_ledger', 1)->first();
                if ($payment) {
                    $payment->amount = (int) $payment->amount + (int) $extraAmt;
                    $payment->save();
                }

                $payment = new PaymentController();
                $payment->store($paymentsData);

                return $this->response('Room/Amount changed Successfully.', null, true);
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
            $res           = [];
            $bookedRoom    = BookedRoom::find($request->eventId);
            $bookedRoomIds = BookedRoom::whereBookingId($bookedRoom->booking_id)->pluck('id');
            $bookingModel =  $this->gerBookingModel($bookedRoom->booking_id);

            if ($bookingModel->booking_status == 0) {
                return $this->response('oops cant change already guest checkout.', null, true);
            }

            foreach ($bookedRoomIds as $bookedRoomId) {
                $res[] = $this->changeDateByDragProcess($request, $bookedRoomId);
            }
            // return $res;
            $all_room_Total_amount = array_sum(array_column($res, 'all_room_Total_amount'));
            $nights                = $res[0]['nights'];
            $paid_amounts          = $res[0]['paid_amounts'];
            $extraDaysAmount       = $res[0]['extraDaysAmount'];

            $transactionData = [
                'booking_id'        => $bookedRoom->booking_id,
                'customer_id'       => $bookedRoom->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $bookedRoom->company_id ?? '',
                'payment_method_id' => 7,
                'desc'              => 'room extends amount',
            ];

            (new TransactionController)->store($transactionData, $extraDaysAmount, 'debit');
            $transactionSummary = (new TransactionController)->getTransactionSummaryByBookingId($bookedRoom->booking_id);
            Booking::find($bookedRoom->booking_id)->update([
                'check_in'              => $request->start,
                'check_out'             => $request->end,
                'total_days'            => $nights,
                // 'total_price'           => $all_room_Total_amount * $nights,
                'total_price'           => $transactionSummary['sumDebit'],

                'grand_remaining_price' => $transactionSummary['balance'],
                'balance'               => $transactionSummary['balance'],
                'remaining_price'       => $transactionSummary['balance'],
                'paid_amounts'          => $transactionSummary['sumCredit'],

            ]);

            $period = CarbonPeriod::create($request->start, $this->checkOutDate(date('Y-m-d', strtotime($request->end))));
            OrderRoom::whereBookedRoomId($bookedRoom->id)->delete();
            foreach ($period as $date) {
                $bookedRoom['date']           = $date->format('Y-m-d');
                $bookedRoom['booked_room_id'] = $bookedRoom->id;
                OrderRoom::create($bookedRoom->toArray());
            }

            $payment = Payment::whereBookingId($bookedRoom->booking_id)->where('company_id', $bookedRoom->company_id)->where('is_city_ledger', 1)->first();
            if ($payment) {
                $payment->amount = (int) $payment->amount + (int) $extraDaysAmount;
                $payment->save();
            }

            return $this->response('Date changed Successfully.', null, true);

            return $this->response('DataBase Error in status change', null, true);
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    private function changeDateByDragProcess($request, $bookedRoomId)
    {
        try {
            $end = $request->end; //date('Y-m-d', strtotime($request->end . '+1day'));
            // $end             = date('Y-m-d', strtotime($request->end . '+1day'));
            $bookedRoom      = BookedRoom::find($bookedRoomId);
            $booking         = Booking::find($bookedRoom->booking_id);
            $nightsCal       = Carbon::parse(date('Y-m-d', strtotime($request->start)))->diffInDays(date('Y-m-d', strtotime($end)));
            $nights          = $nightsCal; // - 1;
            $bookingDays     = Carbon::parse(date('Y-m-d', strtotime($booking->check_in)))->diffInDays(date('Y-m-d', strtotime($booking->check_out)));
            $extraDays       = $nights - $bookingDays;
            $extraDaysAmount = $extraDays * $booking->all_room_Total_amount;

            [
                'request->start'               => $request->start,
                'end'                          => $end,
                'booking->total_days'          => $booking->total_days,
                'nights'                       => $nights,
                'extraDays'                    => $extraDays,
                'bookedRoom->total *  $nights' => $bookedRoom->total * $nights,

                'booking_check_in'             => $booking->check_in,
                'booking_check_out'            => $booking->check_out,
                'bookingdays'                  => ($nights - $bookingDays),
                'roomamount'                   => $booking->all_room_Total_amount,
                'extradateamount'              => ($nights - $bookingDays) * $booking->all_room_Total_amount,

                'check_in'                     => $request->start,
                'check_out'                    => $end,
                'total_days'                   => $nights,
                'total_price'                  => $booking->all_room_Total_amount * $nights,
                'grand_remaining_price'        => ($booking->all_room_Total_amount * $nights) - $booking->paid_amounts,

                'more'                         => $booking->total_days < $nights,
                'less'                         => $booking->total_days > $nights,
                '-abs($extraDays)'             => -abs($extraDays),
                '$extraDays < 0'               => $extraDays < 0,
                '$extraDaysAmount'             => $extraDaysAmount,
            ];

            $updated = $bookedRoom->update([
                'check_in'    => $request->start,
                'check_out'   => $end,
                'days'        => $nights,
                'grand_total' => $bookedRoom->total * $nights,
            ]);

            $period = CarbonPeriod::create($request->start, $this->checkOutDate(date('Y-m-d', strtotime($end))));
            if ($updated) {
                OrderRoom::whereBookedRoomId($bookedRoom->id)->delete();
                foreach ($period as $date) {
                    $bookedRoom['date']           = $date->format('Y-m-d');
                    $bookedRoom['booked_room_id'] = $bookedRoom->id;
                    OrderRoom::create($bookedRoom->toArray());
                }
            }

            return [
                'all_room_Total_amount'       => $booking->all_room_Total_amount,
                'extraDays'       => $extraDays,
                'extraDaysAmount'       => $extraDaysAmount,
                'nights'                => $nights,
                'paid_amounts'          => $booking->paid_amounts,
                'all_room_Total_amount' => $bookedRoom->total_with_tax,
            ];
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
            ->where('booking_status', '!=', 1)
            ->paginate($request->per_page ?? 20);
    }

    public function getReservationList(Request $request, $status)
    {
        $model = Booking::query()
            ->latest()
            ->filter(request('search'));
        switch ($status) {
            case 'upcoming':
                $model->where('booking_status', '=', 1);
                break;
            case 'check_out':
                $model->where(function ($q) {
                    $q->where('booking_status', '=', 3);
                    $q->orWhere('booking_status', '=', 0);
                });
                break;
            case 'in_house':
                $model->where('booking_status', '=', 2);
                break;
            default:
                abort(400, 'Invalid status');
        }

        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name,document',
            ])
            ->where('company_id', $request->company_id)
            ->paginate($request->per_page ?? 20);
    }

    public function upComingReservationList(Request $request)
    {
        return $this->getReservationList($request, 'upcoming');
    }

    public function checkOutReservationList(Request $request)
    {
        return $this->getReservationList($request, 'check_out');
    }

    public function inHouseReservationList(Request $request)
    {
        return $this->getReservationList($request, 'in_house');
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
            $model->WhereDate('check_in', $request->date);
        } else if ($request->filled('status') && $request->status == 2) {
            $model->where('booking_status', $request->status);
            $model->whereDate('check_in', '<=', $request->date);
        } else if ($request->filled('status') && $request->status == 3) {
            $model->where('booking_status', $request->status);
            $model->WhereDate('check_out', $request->date);
        }

        $model->where('booking_status', '!=', -1);
        $model->where('booking_status', '!=', 0);
        $model->where('booking_status', '<=', 2);
        return $model->paginate($request->per_page ?? 20);
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
