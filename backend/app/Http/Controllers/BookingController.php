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
use App\Models\HallBookings;
use App\Models\Holiday;
use App\Models\IdCardType;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TaxSlabs;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\Weekend;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function verifyBooking(Request $request)
    {
        return Booking::where([
            "id" => $request->booking_id ?? 0,
            "customer_id" => $request->customer_id ?? 0,
        ])->update(["verified" => Booking::VERIFICATION_COMPLETED]);
    }

    public function updatePicAndSign()
    {
        Logger::log("info", "FRANCIS " . request('company_id'));
        try {
            $customer_id =  Booking::where(
                [
                    "verified" => Booking::VERIFICATION_REQUIRED,
                    "company_id" => request('company_id'),
                ]
            )->orderByDesc("id")->value("customer_id");

            $customer = [];

            if (request('captured_photo')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('captured_photo')));
                $imageName = "captured_photo-" . time() . ".png";
                $publicDirectory = public_path("captured_photo");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["captured_photo"] = $imageName;
            }

            if (request('id_frontend_side')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('id_frontend_side')));
                $imageName = "id_frontend_side-" . time() . ".png";
                $publicDirectory = public_path("id_frontend_side");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["id_frontend_side"] = $imageName;
            }

            if (request('id_backend_side')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('id_backend_side')));
                $imageName = "id_backend_side-" . time() . ".png";
                $publicDirectory = public_path("id_backend_side");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["id_backend_side"] = $imageName;
            }

            if (request('sign')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('sign')));
                $imageName = "sign-" . time() . ".png";
                $publicDirectory = public_path("sign");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["sign"] = $imageName;
            }

            // $customer["company_id"] = request('company_id');

            return Customer::where("id", $customer_id)->update($customer);
        } catch (\Exception $e) {
            return $this->getMessage();
        }
    }
    public function getLattestCustomerInfo($booking)
    {
        return Customer::whereHas("booking", fn($q) => $q->where("id", $booking))->first();
    }
    public function index(Request $request)
    {
        return Booking::with(["customer:id,first_name,last_name", "room"])
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->orderByDesc("id")
            ->paginate($request->per_page ?? 50);
    }

    public function getBookedRoomList()
    {
        return BookedRoom::where('booking_id', request('booking_id'))
            ->where('booking_status', BookedRoom::BOOKED)
            ->orderByDesc("room_no")
            ->get();
    }

    public function booking_validate(BookingRequest $request)
    {
        return $this->response('Booking validated.', null, true);
    }

    public function document_validate(DocumentRequest $request)
    {
        return $this->response('Document validated.', null, true);
    }
    // public function widgetRoomBooking(Request $request)
    // {
    //     $response =  $this->store($request);

    //     $responseArray = json_decode($response, true);
    //     if (isset($responseArray['data'])) {
    //         $booking_id = $responseArray['data'];
    //         if (isset($request['api_json_reference_number'])) {
    //             $api_json_reference_number = $request['api_json_reference_number'];
    //             Booking::where("id", $booking_id)->update(["widget_confirmation_number" => $api_json_reference_number]);
    //         }
    //     }

    //     return   $response;
    // }



    public function getNotificationCount(Request $request)
    {

        $model = Booking::query();
        $model->where('company_id', $request->company_id);

        $model->whereNot('widget_confirmation_number',   null);
        $model->where('booking_date', "=", date('Y-m-d'));


        $data['online_booking_count'] =  $model->pluck("id");


        return $data;
    }

    public function store(Request $request)
    {




        $diff_in_seconds = strtotime($request->check_in) - strtotime(date('Y-m-d'));
        if ($diff_in_seconds < 0) {
            return response()->json(['data' => 'Booking Date is invalid', 'status' => false]);
        }

        $booking = null;

        //verify is booking  availalbe with date and room number

        $bookedRoomsCount = BookedRoom::whereDate('check_in', '<=', $request->check_in)
            ->WhereDate('check_out', '>=', $request->check_out)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
                $q->where('room_id', $request->selectedRooms[0]['room_id']);
            })->count();



        if ($bookedRoomsCount > 0) {
            return response()->json(['error' => 'Room is not availalbe on this Date']); // return a user-friendly error 
        }


        // DB::beginTransaction();
        $error = '';
        try {
            $customer_id = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            //$booking = $this->storeBooking($request);

            $bookingArray = $this->storeBooking($request);
            $booking_reservation_number =  $bookingArray[1];
            $booking  =  $bookingArray[0];



            if ($booking) {
                $error = $this->storeBookedRooms($request, $booking);

                //DB::commit();

                //recalculating Tax based on discount 
                $error = (new ManagementController())->generateOccupancyRateByBooking($request);
                $error = (new RecalculateTaxController())->UpdateTaxWithID($booking->id);

                try {

                    if ($request->filled("payment_reference_id")) {
                        $data = [];
                        $data['payment_reference_id'] = $request->payment_reference_id;
                        $data['payment_response'] =  json_encode($request->payment_response);

                        Booking::whereId($booking->id)->update($data);
                    }
                } catch (\Exception $e) {
                }
            } else {
            }




            // all good
        } catch (\Exception $e) {
            // DB::rollback();
            return response()->json(['error' => 'An error occurred. Please try again.' . $e->getMessage()]); // return a user-friendly error 
        }


        return response()->json(['data' => $booking->id, 'booking_reservation_number' => $booking_reservation_number, 'status' => true]);
    }

    public function storeBooking($request)
    {
        //try {
        //return DB::transaction(function () use ($request) {


        $merge_food_in_room_price = (int)$request->merge_food_in_room_price;
        $data = [];
        $data = $request->only(Booking::bookingAttributes());
        $data['booking_date'] = now();
        $data['merge_food_in_room_price'] =   $merge_food_in_room_price;
        $data['payment_status'] = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
        $data['remaining_price'] = (float) $request->total_price - (float) $request->advance_price;
        $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
        $data['reservation_no'] = $this->getReservationNumber($data);


        if ($request->filled('api_json_reference_number')) {
            $data['widget_confirmation_number'] = $request->api_json_reference_number;
        }

        $booked = Booking::create($data);

        if ($booked) {
            $arr = $request->allFoods;

            $final_arr = [
                'breakfast' => [
                    'adult' => array_sum(array_column(array_column($arr, 'breakfast'), 'adult')),
                    'child' => array_sum(array_column(array_column($arr, 'breakfast'), 'child')),
                    'baby' => array_sum(array_column(array_column($arr, 'breakfast'), 'baby')),
                ],
                'lunch' => [
                    'adult' => array_sum(array_column(array_column($arr, 'lunch'), 'adult')),
                    'child' => array_sum(array_column(array_column($arr, 'lunch'), 'child')),
                    'baby' => array_sum(array_column(array_column($arr, 'lunch'), 'baby')),
                ],
                'dinner' => [
                    'adult' => array_sum(array_column(array_column($arr, 'dinner'), 'adult')),
                    'child' => array_sum(array_column(array_column($arr, 'dinner'), 'child')),
                    'baby' => array_sum(array_column(array_column($arr, 'dinner'), 'baby')),
                ],
            ];

            Food::create([
                'booking_id' => $booked->id,
                'breakfast' => $final_arr['breakfast'],
                'lunch' => $final_arr['lunch'],
                'dinner' => $final_arr['dinner'],
                'company_id' => $request->company_id,
            ]);

            $transactionData = [
                'booking_id' => $booked->id,
                'customer_id' => $booked->customer_id ?? '',
                'date' => now(),
                'company_id' => $request->company_id ?? '',
                'desc' => 'rooms booking amount',
                'reference_number' => $request->reference_number,
                'payment_method_id' => 7,
                'user_id' => $request->user_id,
            ];

            //Transaction
            $payment = new TransactionController();
            $payment->store($transactionData, $request->total_price, 'debit');

            if ($request->advance_price && $request->advance_price > 0) {
                $transactionData['desc'] = 'advance payment';
                $transactionData['payment_method_id'] = $booked->payment_mode_id;

                $payment->store($transactionData, $request->advance_price, 'credit');
            }
            //End Transaction
            if ((float) $booked->advance_price == 0) {

                if (($booked->paid_by && $booked->paid_by == 2) || ($booked->type != 'Walking' && $booked->type != 'Complimentary')) {

                    $agentsData = [
                        'booking_id' => $booked->id,
                        'customer_id' => $booked->customer_id ?? '',
                        'type' => $booked->type ?? '',
                        'source' => $booked->source,
                        'reference_no' => $booked->reference_no ?? '',
                        'amount' => $booked->total_price ?? '',
                        'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                        'company_id' => $request->company_id ?? '',
                        'is_paid' => $booked->paid_by == 1 ? 2 : 0,
                    ];
                    $payment = new AgentsController();
                    $payment->store($agentsData);

                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => 7,
                        'description' => $booked->source,
                        'amount' => $booked->remaining_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 1,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                } else {
                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => 7,
                        'description' => $booked->source,
                        'amount' => $booked->remaining_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 1,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }
            } else {

                if ($request->total_price >= $request->advance_price) {

                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => $booked->payment_mode_id,
                        'description' => 'advance payment',
                        'amount' => $booked->advance_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 0,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }

                $paymentsData = [
                    'booking_id' => $booked->id,
                    'payment_mode' => 7,
                    'description' => 'pending payment',
                    'amount' => $booked->remaining_price,
                    'type' => 'room',
                    'room' => $booked->rooms,
                    'company_id' => $request->company_id,
                    'is_city_ledger' => 1,
                ];
                $payment = new PaymentController();
                $payment->store($paymentsData);

                $agentsData = [
                    'booking_id' => $booked->id,
                    'customer_id' => $booked->customer_id ?? '',
                    'type' => 'Customer' ?? '',
                    'source' => $booked->source,
                    'reference_no' => $booked->reference_no ?? '',
                    'amount' => $booked->total_price ?? '',
                    'agent_paid_amount' => $booked->advance_price ?? '',
                    'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                    'company_id' => $request->company_id ?? '',
                ];
                $payment = new AgentsController();
                $payment->store($agentsData);
            }

            if ($request->gst_number) {
                (new TaxableController())->storeTaxableInvoice($booked);
            }
        }

        return [$booked, $data['reservation_no']];

        return $this->response('Room Booked Successfully.', $booked, true);
        // });
        // } catch (\Throwable $th) {
        //     return $th;
        //     Logger::channel("custom")->error("BookingController: " . $th);
        //     return ["done" => false, "data" => "DataBase Error booking"];
        // }
    }

    public function getReservationNumber($data)
    {
        $company_id = $data['company_id'];
        $starting_value = 00001;
        $model = Booking::query();

        // (int) $counter = $model->where('company_id', $company_id)->latest('reservation_no')->value('reservation_no') ?? $starting_value;
        (int) $counter = $model->where('company_id', $company_id)->orderBy('id', 'desc')->first()->reservation_no ?? $starting_value;


        $exist = $model->where('company_id', $company_id)->where('reservation_no', $counter)->exists();

        if ($exist) {

            $counter = $counter + HallBookings::where('company_id', $company_id)->count();

            return (int) ++$counter;
        } else {
            return $starting_value;
        }
    }

    public function getOrderRoomDiscount($roomCount = 0, $roomModel, $price = 0)
    {
        // dd($roomModel->room_discount / $roomCount);
        return $price;
    }

    public function storeBookedRooms($request, $booking)
    {
        try {
            $rooms = $request->only('selectedRooms');

            foreach ($rooms['selectedRooms'] as $room) {

                $room['booking_id'] = $booking->id;
                $room['customer_id'] = $booking->customer_id;
                $room['booking_status'] = $booking->booking_status;


                $priceList = $room['priceList'];

                unset($room['priceList']);
                unset($room['meal_name']);

                $bookedRoomId = BookedRoom::create($room);

                $orderRooms = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));
                $singleDayDiscount = ($room['room_discount'] / count($priceList));
                $singleDayExtraAmount = ($room['room_extra_amount'] / count($priceList));
                // $singleDayPrice = ($room['price'] / count($priceList));

                foreach ($priceList as $list) {
                    $singleDayPrice = $list['room_price'];
                    // Recalculation start
                    $taxArray = $this->reCalculatePrice($list['price'] - $singleDayDiscount + $singleDayExtraAmount);

                    $price_adjusted_after_dsicount = $taxArray['basePrice'];
                    $list['tax'] = $taxArray['gstAmount'];
                    // Recalculation end

                    $orderRooms['price_adjusted_after_dsicount'] = $price_adjusted_after_dsicount;
                    $orderRooms['date'] = $list['date'];

                    $orderRooms['room_discount'] = $singleDayDiscount;
                    $orderRooms['after_discount'] = $list['price'] - $orderRooms['room_discount'] + $singleDayExtraAmount;

                    $price = $orderRooms['after_discount'];

                    $orderRooms['total'] = $price + $bookedRoomId->food_plan_price;
                    $orderRooms['grand_total'] = $price + $bookedRoomId->food_plan_price;

                    $orderRooms['total_with_tax'] = $price;

                    $orderRooms['price'] =  $price;

                    $orderRooms['days'] = 1;
                    $orderRooms['room_tax'] = $list['tax'];
                    $orderRooms['sgst'] = $list['tax'] / 2;
                    $orderRooms['cgst'] = $list['tax'] / 2;
                    $orderRooms['booked_room_id'] = $bookedRoomId->id;
                    $orderRooms['customer_id'] = $bookedRoomId->customer_id;
                    $orderRooms['meal'] = $bookedRoomId->meal;
                    $orderRooms['no_of_adult'] = $bookedRoomId->no_of_adult;
                    $orderRooms['no_of_child'] = $bookedRoomId->no_of_child;
                    $orderRooms['no_of_baby'] = $bookedRoomId->no_of_baby;
                    $orderRooms['food_plan_id'] = $bookedRoomId->food_plan_id;
                    $orderRooms['food_plan_price'] = $bookedRoomId->food_plan_price;
                    $orderRooms['extra_bed_qty'] = $bookedRoomId->extra_bed_qty;
                    $orderRooms['early_check_in'] = $bookedRoomId->early_check_in;
                    $orderRooms['late_check_out'] = $bookedRoomId->late_check_out;

                    $orderRooms['breakfast'] = $bookedRoomId->breakfast ?? 0;
                    $orderRooms['lunch'] = $bookedRoomId->lunch ?? 0;
                    $orderRooms['dinner'] = $bookedRoomId->dinner ?? 0;

                    OrderRoom::create($orderRooms);
                }
            }

            if (app()->isProduction()) {
                $customer = Customer::find($booking->customer_id);
                (new WhatsappNotificationController())->whatsappNotification($booking, $rooms['selectedRooms'], $customer, 'booking');
            }

            return $rooms;
            return $this->response('Room Booked Successfully.', $rooms, true);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function reCalculatePrice($finalAmountWithDiscount)
    {
        //$finalAmountWithDiscount = 4000;
        $tax = 12; //default

        $calculationStatus = false;
        $tax = 12;
        if ($finalAmountWithDiscount >= 2800) {
            $tax = 18;
        } else if ($finalAmountWithDiscount >= 9600) {
            $tax = 28;
        }
        $array = $this->gstTax($finalAmountWithDiscount, $tax);

        // if ($array['basePrice'] <= 2499 && $tax == 12) {
        //     $calculationStatus = true;
        // }
        // if ($calculationStatus == false) {
        //     $tax = 18;
        //     $array = $this->gstTax($finalAmountWithDiscount, $tax);

        //     if ($array['basePrice'] > 2499 && $array['basePrice'] <= 7499) {
        //         $calculationStatus = true;
        //     }
        // }
        // if ($calculationStatus == false) {
        //     $tax = 28;
        //     $array = $this->gstTax($finalAmountWithDiscount, $tax);

        //     if ($array['basePrice'] > 7499) {
        //         $calculationStatus = true;
        //     }
        // }

        return $array;
    }
    public function gstTax($finalAmountWithDiscount, $tax)
    {
        $basePrice = ($finalAmountWithDiscount * 100) / (100 + $tax);
        $gstAmount = $finalAmountWithDiscount - $basePrice;

        // $gstAmount = ($finalAmountWithDiscount / (1 + $tax));
        // $basePrice = $finalAmountWithDiscount - $gstAmount;

        return ["basePrice" => round($basePrice, 2), "gstAmount" => round($gstAmount, 2), "tax" => $tax];
    }
    public function reCalculatePriceTest()
    {

        $finalAmountWithDiscount = 3360;
        $tax = 12; //default

        $calculationStatus = false;
        $tax = 12;
        if ($finalAmountWithDiscount >= 2800) {
            $tax = 18;
        } else if ($finalAmountWithDiscount >= 9600) {
            $tax = 28;
        }
        $array = $this->gstTax($finalAmountWithDiscount, $tax);

        // if ($array['basePrice'] <= 2499 && $tax == 12) {
        //     $calculationStatus = true;
        // }
        // if ($calculationStatus == false) {
        //     $tax = 18;
        //     $array = $this->gstTax($finalAmountWithDiscount, $tax);

        //     if ($array['basePrice'] > 2499 && $array['basePrice'] <= 7499) {
        //         $calculationStatus = true;
        //     }
        // }
        // if ($calculationStatus == false) {
        //     $tax = 28;
        //     $array = $this->gstTax($finalAmountWithDiscount, $tax);

        //     if ($array['basePrice'] > 7499) {
        //         $calculationStatus = true;
        //     }
        // }

        return $array;
    }
    public function storeDocument(Request $request)
    {



        $booking = Booking::find($request->booking_id);
        $customer = Customer::find($booking->customer_id);
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

    public function storeDocumentTest(Request $request)
    {

        // return $request->all();

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path = $file->storeAs('public/test/doc', $fileName);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path = $file->storeAs('public/test/img', $fileName);
        }

        return $this->response('Room Booked Successfully.', null, true);
    }

    public function updateByDrag(Request $request)
    {
        // return $request->only('check_in', 'check_out', 'id');
        return Booking::where('id', $request->id)->update($request->only('check_in', 'check_out'));
    }

    public function checkOutDate($date)
    {
        $date = date_create($date);
        date_modify($date, "-1 days");
        return date_format($date, "Y-m-d");
    }

    private function updateTransaction($booking, $request, $desc = "", $mode, $amt)
    {
        $transactionData = [
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id ?? '',
            'date' => now(),
            'company_id' => $booking->company_id ?? '',
            'payment_method_id' => $request->payment_mode_id,
            'desc' => $desc,
            'reference_number' => $request->reference_number,
            'user_id' => $request->user_id,
        ];
        (new TransactionController())->store($transactionData, $amt, $mode);
        (new TransactionController())->updateBookingByTransactions($booking->id, 0);
    }

    private function updatePayment($booking, $request, $amt, $desc = "")
    {
        $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
        if ($payment) {
            $payment->amount = (float) $payment->amount - (float) $amt;
            $payment->save();
        }

        $paymentsData = [
            'booking_id' => $booking->id,
            'payment_mode' => $request->payment_mode_id,
            'description' => $desc,
            'amount' => $amt,
            'company_id' => $booking->company_id,
            'type' => 'room',
            'room' => $booking->rooms,
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
            $isExistCustomer = false;
            if (!is_null($customer['contact_no'])) {
                $isExistCustomer = Customer::whereContactNo($customer['contact_no'])->whereCompanyId($customer['company_id'])->first();
            }
            $id = "";
            if ($isExistCustomer) {
                $id = $isExistCustomer->id;
                $isExistCustomer->update($customer);
            } else {

                if (request('id_frontend_side')) {
                    $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('id_frontend_side')));
                    $imageName = "id_frontend_side-" . time() . ".png";
                    $publicDirectory = public_path("customer_id_pic");
                    if (!file_exists($publicDirectory)) {
                        mkdir($publicDirectory);
                    }
                    file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                    $customer["id_frontend_side"] = $imageName;
                }

                if (request('id_backend_side')) {
                    $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('id_backend_side')));
                    $imageName = "id_backend_side-" . time() . ".png";
                    $publicDirectory = public_path("customer_id_pic");
                    if (!file_exists($publicDirectory)) {
                        mkdir($publicDirectory);
                    }
                    file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                    $customer["id_backend_side"] = $imageName;
                }

                $record = Customer::create($customer);
                $id = $record->id ?? 0;
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

            // session(['isCheckInSes' => true]);

            $booking_id = $request->booking_id;
            $booking = Booking::find($booking_id);
            $customer = Customer::find($request->customer_id);
            $booking->check_in_price = $request->new_payment;
            $booking->booking_status = 2;
            $booking->id_card_no = $request->id_card_no;
            $booking->expired = $request->expired;
            $booking->id_card_type = IdCardType::find($request->id_card_type_id)->name ?? "";
            // $booking->check_in = date('Y-m-d H:i');
            $newBookingCheckIn = date('Y-m-d H:i');


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
                $customer->dob = date("Y-m-d");
                $customer->save();
                $this->updateTransaction($booking, $request, 'check in payment', 'credit', $request->new_payment);
                $this->updatePayment($booking, $request, $request->new_payment, 'checkin payment');
                BookedRoom::where("booking_id", $booking_id)->where("room_id", $request->room_id)->update(['check_in' => $newBookingCheckIn, 'booking_status' => 2]);
                // if (app()->isProduction()) {
                //     $customer = Customer::find($booking->customer_id);
                //     (new WhatsappNotificationController())->checkInNotification($booking, $customer);
                // }
                $customerData = $request->only(Customer::customerAttributes());
                $customerData['id'] = $request->customer_id;
                $this->customerUpdateById($customerData);

                $fields = [
                    "title"     => ucfirst($customer['title']) ?? 'Mr',
                    "full_name" => ucfirst($customer['full_name']) ?? 'Guest',
                    "check_in"  => date('d-M-y H:i', strtotime($booking->check_in)),
                    "check_out" => date('d-M-y H:i', strtotime($booking->check_out)),
                    "email" => $customer['email'],
                    "whatsapp" => $customer->whatsapp,
                    // "location" => $company->map, from company model

                ];

                $this->sendMailIfRequired(Template::WHEN_CUSTOMER_ARRIVED, $fields);
                $this->sendWhatsappIfRequired(Template::WHEN_CUSTOMER_ARRIVED, $fields);

                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }

            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Exception $e) {

            return response()->json(['data' => '', 'message' => $e->getMessage(), 'status' => false]);
            // throw $th;
        }
    }

    public function quick_check_in_room(Request $request)
    {
        try {

            // session(['isCheckInSes' => true]);

            $booking_id = $request->booking_id;
            $booking = Booking::find($booking_id);
            $customer = Customer::find($request->customer_id);
            $booking->check_in_price = $request->new_payment;
            $booking->booking_status = 2;
            $booking->id_card_no = $request->id_card_no;
            $booking->expired = $request->expired;
            $booking->id_card_type = IdCardType::find($request->id_card_type_id)->name ?? "";
            $booking->check_in = date('Y-m-d H:i');
            $newBookingCheckIn = date('Y-m-d H:i');


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
                $customer->dob = date("Y-m-d");
                $customer->save();
                $this->updateTransaction($booking, $request, 'check in payment', 'credit', $request->new_payment);
                $this->updatePayment($booking, $request, $request->new_payment, 'checkin payment');
                BookedRoom::where("booking_id", $booking_id)->whereIn("room_no", $request->room_nos)->update(['check_in' => $newBookingCheckIn, 'booking_status' => 2]);
                // if (app()->isProduction()) {
                //     $customer = Customer::find($booking->customer_id);
                //     (new WhatsappNotificationController())->checkInNotification($booking, $customer);
                // }
                $customerData = $request->only(Customer::customerAttributes());
                $customerData['id'] = $request->customer_id;
                $this->customerUpdateById($customerData);

                $fields = [
                    "title"     => ucfirst($customer['title']) ?? 'Mr',
                    "full_name" => ucfirst($customer['full_name']) ?? 'Guest',
                    "check_in"  => date('d-M-y H:i', strtotime($booking->check_in)),
                    "check_out" => date('d-M-y H:i', strtotime($booking->check_out)),
                    "email" => $customer['email'],
                    "whatsapp" => $customer->whatsapp,
                    // "location" => $company->map, from company model

                ];

                $this->sendMailIfRequired(Template::WHEN_CUSTOMER_ARRIVED, $fields);
                $this->sendWhatsappIfRequired(Template::WHEN_CUSTOMER_ARRIVED, $fields);

                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }

            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Exception $e) {

            return response()->json(['data' => '', 'message' => $e->getMessage(), 'status' => false]);
            // throw $th;
        }
    }

    public function check_out_room(Request $request)
    {
        try {

            // session(['isCheckoutSes' => true]);

            $booking_id = $request->booking_id;
            $booking = Booking::where('company_id', $request->company_id)->find($booking_id);
            $customer = Customer::find($booking->customer_id);
            if ($request->discount > 0) {
                $this->updateTransaction($booking, $request, 'discount', 'debit', -abs($request->discount));
                $bookedRoom = BookedRoom::whereBookingId($booking_id)->first();
                $bookedRoom->increment('room_discount', $request->discount);
            }


            $transactionData = [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id ?? '',
                'date' => now(),
                'company_id' => $booking->company_id ?? '',
                'payment_method_id' => $request->payment_mode_id,
                'desc' => 'check out payment',
                'reference_number' => $request->reference_number,
                'user_id' => $request->user_id,
            ];



            $trans = new TransactionController();

            if ($request->isHall && $request->exceedHoursCharges > 0) {
                $transactionData["desc"] = "additional hours charges";
                $trans->store($transactionData, $request->exceedHoursCharges ?? 0, 'debit');
            }
            // if ($request->full_payment > 0) {
            $trans->store($transactionData, $request->full_payment ?? 0, 'credit');
            // }

            $booking = Booking::find($booking_id);
            if ($booking) {
                $customer = Customer::find($booking->customer_id);

                if ($booking->balance > 0) {
                    $booking->payment_status = 0;
                    $booking->remaining_price = (int) $booking->remaining_price - (int) $request->full_payment;
                    $booking->grand_remaining_price = (int) $booking->remaining_price + (int) $booking->total_posting_amount;

                    $paymentsData = [
                        'booking_id' => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description' => 'checkout payment',
                        'amount' => $request->full_payment,
                        'type' => 'customer',
                        'room' => $booking->rooms,
                        'company_id' => $booking->company_id,
                        'is_city_ledger' => 0,
                        'created_at' => now(),
                    ];
                    $payment = Payment::whereBookingId($booking->id)
                        ->where('company_id', $booking->company_id)->where('is_city_ledger', 1)
                        ->first();
                    if ($payment) {
                        $payment->amount = (int) $booking->balance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                } else {
                    $booking->payment_status = 1;
                    $booking->full_payment = $booking->paid_amounts;
                    $booking->remaining_price = 0;
                    $booking->grand_remaining_price = 0;
                    $booking->total_posting_amount = 0;

                    $paymentsData = [
                        'booking_id' => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description' => 'checkout payment',
                        'amount' => $request->full_payment,
                        'type' => 'customer',
                        'room' => $booking->rooms,
                        'company_id' => $booking->company_id,
                        'is_city_ledger' => 0,
                        'created_at' => now(),
                    ];
                    $payment = Payment::whereBookingId($booking->id)
                        ->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $booking->balance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }
                $booking->booking_status = 3;
                $booking->check_out = date('Y-m-d H:i');
                $booking->save();

                BookedRoom::whereBookingId($booking_id)->update(
                    [
                        "booking_status" => 3,
                        "check_out" => date('Y-m-d H:i')
                    ]
                );

                // if (app()->isProduction()) {
                //     (new WhatsappNotificationController())->checkOutNotification($booking, $customer);
                // }


                $fields = [
                    "title"     => ucfirst($customer['title']) ?? 'Mr',
                    "full_name" => ucfirst($customer['full_name']) ?? 'Guest',
                    "check_in"  => date('d-M-y H:i', strtotime($booking->check_in)),
                    "check_out" => date('d-M-y H:i', strtotime($booking->check_out)),
                    "email" => $customer['email'],
                    "whatsapp" => $customer->whatsapp,
                    // "location" => $company->map, from company model

                ];

                $this->sendMailIfRequired(Template::AFTER_CHECKOUT, $fields);
                $this->sendWhatsappIfRequired(Template::AFTER_CHECKOUT, $fields);

                return response()
                    ->json(['bookingId' => $booking_id, 'message' => 'Successfully Paid', 'status' => true]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function payingAdvance(Request $request)
    {
        try {
            $booking = Booking::find($request->booking_id);
            $transactionData = [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id ?? '',
                'date' => now(),
                'company_id' => $booking->company_id ?? '',
                'payment_method_id' => $request->payment_mode_id,
                'desc' => $request->input('desc', 'advance payment'), // $desc 'advance payment',
                'reference_number' => $request->reference_number,
                'user_id' => $request->user_id,
            ];

            $payAmt = $request->new_advance;
            $meth = 'credit';

            if ($payAmt < 0) {
                $meth = 'debit';
            }

            (new TransactionController())->store($transactionData, $payAmt, $meth);
            (new TransactionController())->updateBookingByTransactions($booking->id, 0);

            // $payAmt < 0 ? $booking->advance_price = (int) $booking->advance_price - (int) $payAmt :
            // $booking->advance_price = (int) $booking->advance_price + (int) $payAmt ;

            if ($payAmt > 0) {
                $booking->advance_price = (int) $booking->advance_price + (int) $request->new_advance;
                $booking->save();
            }

            $paymentsData = [
                'booking_id' => $booking->id,
                'payment_mode' => $request->payment_mode_id,
                'description' => 'advance payment',
                'amount' => $request->new_advance,
                'company_id' => $booking->company_id,
                'type' => 'room',
                'room' => $booking->rooms,
            ];

            $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
            if ($payment) {

                $payAmt < 0 ? $payment->amount = (int) $payment->amount + (int) $payAmt : $payment->amount = (int) $payment->amount - (int) $payAmt;
                // $payment->amount = (int) $payment->amount - (int) $payAmt;
                $payment->save();
            }

            $payment = new PaymentController();
            if ($payAmt > 0) {
                $payment->store($paymentsData);
            }

            if (app()->isProduction() && $request->new_advance > 0) {

                $customer = Customer::find($booking->customer_id);
                (new WhatsappNotificationController())
                    ->advancePayingNotification($booking->fresh(), $customer, $request->new_advance, $request->payment_mode_id);
            }

            return response()->json(['data' => '', 'message' => 'Payment Successfully', 'status' => true]);
        } catch (\Throwable $th) {

            echo  " Cron:  .\n" . $th;
            Logger::channel("custom")->error($th);
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
            // throw $th;
        }
    }

    public function payingAmount(Request $request)
    {
        try {
            $booking_id = $request->booking_id;
            $booking = Booking::find($booking_id);
            $rem = (int) $request->remaining_price - (int) $request->new_advance;
            $booking->remaining_price = $rem;
            $booking->grand_remaining_price = (int) $rem + (int) $booking->total_posting_amount;
            $booking->advance_price = $request->new_advance;
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
                        'booking_id' => $booking_id,
                        'payment_mode' => $request->payment_mode_id,
                        'description' => 'advance payment',
                        'amount' => $request->new_advance,
                        'company_id' => $booking->company_id,
                        'type' => 'room',
                        'room' => $booking->rooms,
                    ];

                    $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $payment->amount - (int) $request->new_advance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);

                    $totCredit = Transaction::whereBookingId($booking->id)->where('company_id', $booking->company_id)->sum('credit');
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
        $date_from = date('Y-m-d', strtotime('-7 days', strtotime($request->startDateString)));

        $date_to =  $request->endDateString;

        $search = $request->search;

        return BookedRoom::whereHas('booking', function ($q) use ($request, $date_from, $date_to,) {
            // $q->where('booking_status', '!=', 0);
            $q->where('company_id', $request->company_id);
            $q->where('check_in', '>=', $date_from);
            $q->where('check_in', '<=', $date_to);
        })

            ->when($request->filled('search'), function ($query) use ($search) {
                $query->whereHas('booking.customer', function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('reservation_no', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
                        $query->orWhere('first_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
                        $query->orWhere('last_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
                        $query->orWhere('contact_no',  env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
                    });
                });
            })
            ->with("room")
            ->get(['id', 'room_id', 'booking_id', 'customer_id', 'check_in', 'check_in as start', 'check_out', 'booking_status', 'room_category_type as className']);
    }

    public function events_list1(Request $request)
    {
        return Booking::where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->get(['id', 'room_id', 'customer_id', 'check_in as start', 'check_out as end']);
    }

    public function get_booking(Request $request)
    {
        $bookedRoom = BookedRoom::with(['booking', 'customer', "room"])->where('company_id', $request->company_id)->findOrFail($request->id);
        $bookedRoom->booking->room_id = $bookedRoom->room_id;
        $bookedRoom->booking->room_no = $bookedRoom->room_no;
        $bookedRoom->booking->room_type = $bookedRoom->room_type;
        $bookedRoom->booking->isHall = $bookedRoom->room->room_type->type == "hall" ?? false;
        return $bookedRoom->booking;

        // return response()->json(['booking' => $bookedRoom->booking, 'status' => true]);
    }

    public function get_booked_room(Request $request)
    {

        $bookedRoom = BookedRoom::orderBy("id", "desc")->with(['booking', 'customer', "room"])->where('company_id', $request->company_id)->findOrFail($request->id);
        $bookedRoom->booking->room_id = $bookedRoom->room_id;
        $bookedRoom->booking->room_no = $bookedRoom->room_no;
        $bookedRoom->booking->room_type = $bookedRoom->room_type;
        $bookedRoom->booking->contact_no = $bookedRoom->customer->contact_no;

        // return RoomType::HALL;

        return $bookedRoom;
        // return response()->json(['booking' => $bookedRoom->booking, 'status' => true]);
    }

    public function get_booking_for_modify(Request $request)
    {
        return BookedRoom::with(['booking', 'customer', "room"])->where('company_id', $request->company_id)->findOrFail($request->id);
    }

    public function changeCheckIntoBookingAdmin(Request $request, $id)
    {
        try {
            $company_id = $request->company_id;
            $cancel_checkin_userid = $request->cancel_checkin_userid;
            $cancel_checkin_reason = $request->cancel_checkin_reason;
            $booking_id = $request->booking_id;
            $booked_room_id = $request->booked_room_id;
            //change booking status
            $bookingModel = Booking::where('company_id', $company_id)
                ->where('id', $booking_id)
                ->where('booking_status', 2) //only checkedin status
            ;

            $data1 = [
                'booking_status' => 1,
                'cancel_checkin_reason' => $cancel_checkin_reason,
                'cancel_checkin_datetime' => date('Y-m-d H:i:s'),
                'cancel_checkin_userid' => $cancel_checkin_userid
            ];
            $updatedStatus = $bookingModel->update($data1);

            if ($updatedStatus) {
                //change status on booking_rooms table
                $bookingRoomModel = BookedRoom::where('company_id', $company_id)
                    ->where('id', $booked_room_id)
                    ->where('booking_status', 2) //only checkedin status
                ;
                $data2 = [
                    'booking_status' => 1,
                    'cancel_checkin_reason' => $cancel_checkin_reason,
                    'cancel_checkin_datetime' => date('Y-m-d H:i:s'),
                    'cancel_checkin_userid' => $cancel_checkin_userid
                ];
                $bookingRoomModel->update($data2);

                //change status on booking_rooms table
                $transactionData = Transaction::where('company_id', $company_id)
                    ->where('booking_id', $booking_id)
                    ->where('desc', 'check in payment')
                    ->where('credit', '0.0')
                    ->where('debit', '0.0')
                    ->where('payment_method_id', 1)
                    ->latest()->first();

                $transactionData->delete();

                return $this->response('Room Checkin Information is changed to Booking', null, true);
            } else {
                return $this->response('Something is wrong. Room Checkin Information is not updated', null, false);
            }

            return $this->response('Something is wrong. Room Checkin Information is not updated2', null, false);
        } catch (\Throwable $th) {
            //throw $th;

            return $this->response(json_encode($th), null, false);
        }
    }

    public function cancelRoom(Request $request, $id)
    {
        try {
            $model = BookedRoom::find($id);
            $numberOfRooms = BookedRoom::where('booking_id', $model->booking_id)->count();
            $bookingId = $model->booking_id;

            $bookedRoom = $model;
            if ($bookedRoom) {
                $bookedRoom->reason = $request->reason;
                $bookedRoom->cancel_by = $request->cancel_by;
                $bookedRoom->action = $request->action ?? "Cancel by manual";

                $bookedRoom->status_before_cancelation = $model->booking_status;

                $status_before_cancelation_msg = $model->booking_status == 1 ? "Cancelled Before Check-in" : "Cancelled After Check-in";
                $bookedRoom->status_before_cancelation_msg = $status_before_cancelation_msg;

                $arr = $bookedRoom->toArray();
                $cancel = CancelRoom::create($arr);
                if ($cancel) {
                    OrderRoom::whereBookedRoomId($id)->delete();

                    $transactionData = [
                        'booking_id' => $bookedRoom->booking_id,
                        'customer_id' => $bookedRoom->customer_id ?? '',
                        'date' => now(),
                        'company_id' => $bookedRoom->company_id ?? '',
                        'payment_method_id' => 7,
                        'desc' => "room $model->room_no canceled",
                        'user_id' => $request->cancel_by,
                    ];
                    (new TransactionController())->store($transactionData, -$model->grand_total, 'debit');
                    (new TransactionController())->updateBookingByTransactions($model->booking_id, -$model->grand_total);

                    Booking::find($model->booking_id);
                    $payment = Payment::whereBookingId($bookedRoom->booking_id)
                        ->where('company_id', $bookedRoom->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $payment->amount - (int) $model->grand_total;
                        $payment->save();
                    }
                    $numberOfRooms == 1 ? Booking::where('id', $model->booking_id)->update(['booking_status' => -1]) : null;
                    $model->delete();
                    $rooms = BookedRoom::whereBookingId($bookingId)->pluck('room_no')->toArray();
                    Booking::where('id', $model->booking_id)->update(['rooms' => implode(',', $rooms)]);
                }
            }

            return response()->json(['data' => '', 'message' => 'Successfully canceled', 'status' => true]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setAvailable(Request $request, $id)
    {
        try {
            $booking = Booking::find($id);
            if ($booking) {
                BookedRoom::find($request->bookedRoomId)->update(['booking_status' => 0]);
                $numberDirtRooms = BookedRoom::whereBookingId($booking->id)->where('booking_status', '>=', 3)->count();
                if ($numberDirtRooms == 0) {
                    $booking->update(['booking_status' => 0]);
                }
                // return $this->response($numberDirtRooms, null, true);
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
        $string = implode(', ', $bookedRooms);
        return $string;
    }

    public function getTaxSlab($amount, $company_id)
    {
        $amount = (int) $amount;
        $tax = env('GST_TAX_DEFAULT');


        $TaxSlab = TaxSlabs::where('company_id', $company_id)
            ->where('start_price', '<=', $amount)
            ->where('end_price', '>=', $amount)
            ->pluck('tax');



        if (isset($TaxSlab[0])) {
            $tax = $TaxSlab[0];
        }

        return  $tax;
    }

    private function getRoomTax($amount, $company_id)
    {
        $temp = [];
        // $per = $amount < 2500 ? 12 : 18;
        // if ($amount > 7500) {
        //     $per = 28;
        // }

        $per = $this->getTaxSlab($amount, $company_id);

        $tax = ($amount / 100) * $per;
        $temp['room_tax'] = $tax;
        $temp['total_with_tax'] = (float) $amount + (float) $tax;
        $temp['after_discount'] = $amount;
        $gst = floatval($tax) / 2;
        $temp['cgst'] = $gst;
        $temp['sgst'] = $gst;
        return $temp;
    }

    private function getRoomAmtWithTax($oldRoom, $newRoom, $request)
    {
        $afterDiscount = (float) $newRoom->room_type->price - (float) $oldRoom->room_discount;
        $data = $this->getRoomTax($afterDiscount, $request->company_id);
        $data['total'] = (float) $data['total_with_tax'] + (float) $oldRoom->tot_adult_food + (float) $oldRoom->tot_child_food;
        $data['grand_total'] = (float) $data['total'] * $oldRoom->days;
        $data['price'] = $newRoom->price;
        $data['room_no'] = $newRoom->room_no;
        $data['room_id'] = $newRoom->id;
        $data['room_type'] = $newRoom->room_type->name ?? "";
        $data['check_in'] = date('Y-m-d', strtotime($request->start));
        $data['check_out'] = date('Y-m-d', strtotime($request->end));
        return array_merge($oldRoom->toArray(), $data);
    }

    public function changeRoomByDrag(Request $request)
    {
        try {
            $oldRoom = BookedRoom::without('postings', 'booking')
                ->where('company_id', $request->company_id)->find($request->eventId);
            $bookingModel = $this->getBookingModel($oldRoom->booking_id);
            $newRoom = Room::where('company_id', $request->company_id)->whereRoomNo($request->roomId)->first();

            if ($bookingModel->booking_status == 0) {
                return $this->response('oops cant change already guest checkout.', null, true);
            }

            $numberOfRoom = BookedRoom::whereBookingId($oldRoom->booking_id)->without('postings', 'booking')->count();

            if ($numberOfRoom == 1) {
                $this->changeSingleRoom($oldRoom, $newRoom, $request);


                //recalculating Tax based on discount 
                $RecalObj = new RecalculateTaxController();
                $RecalObj->UpdateTaxWithID($oldRoom->booking_id);

                return $this->response('Room changed Successfully.', false, true);
            }

            return response()->json(
                [
                    'newRoom' => $newRoom,
                    'options' => [
                        'checkIn' => date('Y-m-d', strtotime($request->start)),
                        'checkOut' => date('Y-m-d', strtotime($request->end)),
                        'newRoom' => $oldRoom->customer_id,
                        'booking' => collect($oldRoom->booking->toArray())->except('customer')->all(),
                        'customer' => $oldRoom->booking->customer,
                        'oldRoom' => collect($oldRoom->toArray())->except('customer', 'booking')->all(),
                    ],
                    'status' => true,
                ]
            );

            return $this->response(' Under the working!', null, true);

            if ($newRoom->room_type->name == $oldRoom->room_type) {
                $checkIn = date('Y-m-d', strtotime($request->start));
                $checkOut = date('Y-m-d', strtotime($request->end));
                $oldRoom->update([
                    'room_id' => $newRoom->id,
                    'room_no' => $newRoom->room_no,
                    'room_type' => $newRoom->room_type->name,
                ]);
                BookedRoom::whereBookingId($oldRoom->booking_id)->update([
                    'check_in' => date('Y-m-d 11:00', strtotime($checkIn)),
                    'check_out' => date('Y-m-d 11:00', strtotime($checkOut)),
                ]);
                Booking::find($oldRoom->booking_id)->update([
                    'check_in' => date('Y-m-d', strtotime($checkIn)),
                    'check_out' => date('Y-m-d 11:00', strtotime($checkOut)),
                ]);

                $bookedRoomIds = BookedRoom::whereBookingId($oldRoom->booking_id)->pluck('id');
                foreach ($bookedRoomIds as $bookedRoomId) {
                    $orderRoomModel = OrderRoom::whereBookedRoomId($bookedRoomId)->first()->toArray();
                    $deleted = OrderRoom::whereBookedRoomId($bookedRoomId)->delete();
                    $orderRooms = array_intersect_key($orderRoomModel, array_flip(OrderRoom::orderRoomAttributes()));
                    $period = CarbonPeriod::create($checkIn, $this->checkOutDate($checkOut));
                    foreach ($period as $date) {
                        $orderRooms['date'] = $date->format('Y-m-d');
                        $orderRooms['room_id'] = $newRoom->id;
                        $orderRooms['room_no'] = $newRoom->room_no;
                        $orderRooms['booked_room_id'] = $request->eventId;
                        $orderRooms['booking_id'] = $orderRoomModel['booking_id'];
                        OrderRoom::create($orderRooms);
                    }
                }
                return $this->response('Room/Amount changed Successfully.', null, true);
            }

            $newUpdateRoom = $this->getRoomAmtWithTax($oldRoom, $newRoom, $request);
            $newUpdateRoom['check_out'];
            $extraAmt = $newRoom->room_type->price - $oldRoom->price;
            $bookedRoomAttributes = array_intersect_key($newUpdateRoom, array_flip(BookedRoom::bookedRoomAttributes()));
            $transactionData = [
                'booking_id' => $oldRoom->booking_id,
                'customer_id' => $oldRoom->customer_id ?? '',
                'date' => now(),
                'company_id' => $oldRoom->company_id ?? '',
                'payment_method_id' => 7,
                'desc' => "room/date change",
            ];
            (new TransactionController())->store($transactionData, $extraAmt, 'debit');
            $transactionSummary = (new TransactionController())->getTransactionSummaryByBookingId($oldRoom->booking_id);
            if ($oldRoom->update($bookedRoomAttributes)) {
                $oldRoom->booking->update([
                    'check_in' => $newUpdateRoom['check_in'],
                    'check_out' => $newUpdateRoom['check_out'],
                    'rooms' => $this->getBookedRoomsFromBookingId($oldRoom->booking_id),
                    'total_price' => Booking::find($oldRoom->booking_id)->total_price + $extraAmt,
                    'grand_remaining_price' => $transactionSummary['balance'],
                    'balance' => $transactionSummary['balance'],
                    'remaining_price' => $transactionSummary['balance'],
                    'paid_amounts' => $transactionSummary['sumCredit'],
                ]);
                BookedRoom::whereBookingId($oldRoom->booking_id)->update([
                    'check_in' => date('Y-m-d 11:00', strtotime($newUpdateRoom['check_in'])),
                    'check_out' => date('Y-m-d 11:00', strtotime($newUpdateRoom['check_out'])),
                ]);

                $deleted = OrderRoom::whereBookedRoomId($request->eventId)->delete();

                if ($deleted) {
                    $orderRooms = array_intersect_key($bookedRoomAttributes, array_flip(OrderRoom::orderRoomAttributes()));
                    $period = CarbonPeriod::create($newUpdateRoom['check_in'], $this->checkOutDate($newUpdateRoom['check_out']));
                    foreach ($period as $date) {
                        $orderRooms['date'] = $date->format('Y-m-d');
                        $orderRooms['booked_room_id'] = $request->eventId;
                        $orderRooms['booking_id'] = $newUpdateRoom['booking_id'];
                        OrderRoom::create($orderRooms);
                    }
                }

                $paymentsData = [
                    'booking_id' => $oldRoom->booking_id,
                    'payment_mode' => 7,
                    'description' => 'room/date change',
                    'amount' => $extraAmt,
                    'company_id' => $oldRoom->company_id,
                    'type' => 'room',
                    'room' => $oldRoom->booking->rooms ?? "",
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
            $res = [];
            $bookedRoom = BookedRoom::find($request->eventId);
            $bookedRoomIds = BookedRoom::whereBookingId($bookedRoom->booking_id)->pluck('id');
            $bookingModel = $this->getBookingModel($bookedRoom->booking_id);

            if ($bookingModel->booking_status == 0) {
                return $this->response("oops can't change already guest checkout.", null, true);
            }

            foreach ($bookedRoomIds as $bookedRoomId) {
                //   $this->changeDateByDragProcess($bookingModel, $request, $bookedRoomId);
                $res[] = $this->changeDateByDragProcess($bookingModel, $request, $bookedRoomId);
            }

            // return $res;
            $extraDaysAmount = array_sum(array_column($res, 'extend_room_price'));

            $transactionData = [
                'booking_id' => $bookedRoom->booking_id,
                'customer_id' => $bookedRoom->customer_id ?? '',
                'date' => now(),
                'company_id' => $bookedRoom->company_id ?? '',
                'payment_method_id' => 7,
                'desc' => 'room extends amount',
                'user_id' => $request->user_id ?? '',
            ];
            (new TransactionController())->store($transactionData, $extraDaysAmount, 'debit');
            $transactionSummary = (new TransactionController())->getTransactionSummaryByBookingId($bookedRoom->booking_id);

            OrderRoom::whereBookingId($bookedRoom->booking_id);

            Booking::find($bookedRoom->booking_id)->update([
                'check_in' => $request->start,
                'check_out' => $request->end,
                'total_days' => $res[0]['number_of_nights'] ?? "1",
                // 'total_price'           => $all_room_Total_amount * $nights,
                'total_price' => $transactionSummary['sumDebit'],
                'grand_remaining_price' => $transactionSummary['balance'],
                'balance' => $transactionSummary['balance'],
                'remaining_price' => $transactionSummary['balance'],
                'paid_amounts' => $transactionSummary['sumCredit'],
            ]);

            $payment = Payment::whereBookingId($bookedRoom->booking_id)->where('company_id', $bookedRoom->company_id)->where('is_city_ledger', 1)->first();
            if ($payment) {
                $payment->amount = (int) $payment->amount + (int) $extraDaysAmount;
                $payment->save();
            }

            //recalculating Tax based on discount 
            $RecalObj = new RecalculateTaxController();
            $RecalObj->UpdateTaxWithID($bookedRoom->booking_id);


            return $this->response('Date changed Successfully.', null, true);

            return $this->response('DataBase Error in status change', null, true);
        } catch (\Throwable $th) {
            return $th;
            Logger::channel("custom")->error("BookingController: " . $th);
            return ["done" => false, "data" => "DataBase Error booking"];
        }
    }

    public function modifyBooking(Request $request)
    {
        $room_orders = $request->room_orders;

        $id = $request->id;

        $booking_id = $request->booking_id;

        $check_in = $request->check_in;
        $check_out = $request->check_out;
        $room_id = $request->room_id;
        $room_no = $request->room_no;
        $room_type = $request->room_type;

        $no_of_adult = $request->no_of_adult;
        $no_of_child = $request->no_of_child;

        $breakfast = $request->breakfast;
        $lunch = $request->lunch;
        $dinner = $request->dinner;

        $food_plan_id = $request->food_plan_id;
        $food_plan_price = $request->food_plan_price ?? 0;

        $extra_bed_qty = $request->extra_bed_qty ?? 0;
        $bed_amount = $request->bed_amount;

        $total_days = $request->total_days ?? 0;

        $user_id = $request->user_id;
        $company_id = $request->company_id;

        $booking_remaining_price = $request->booking_remaining_price;
        $booking_total_price = $request->booking_total_price;

        OrderRoom::where('booking_id', $booking_id)->where("booked_room_id", $id)->delete();

        $arr = [];


        foreach ($room_orders as $room_order) {

            $arr[] = [

                'booked_room_id' => $id,
                'company_id' => $company_id,
                'booking_id' => $booking_id,
                'date' => $room_order['date'],
                'room_id' => $room_id,
                'room_no' => $room_no,
                'room_type' => $room_type,
                'price' => $room_order['price'],
                'cgst' => $room_order['tax'] / 2,
                'sgst' => $room_order['tax'] / 2,
                'room_tax' => $room_order['tax'],
                'room_discount' => $room_order['discount'],
                'after_discount' => $room_order['room_price'] - $room_order['discount'],
                'total' => $room_order['price'] + $food_plan_price + $bed_amount,
                'total_with_tax' => $room_order['price'],
                'grand_total' => $room_order['price'] + $food_plan_price + $bed_amount,
                'price_adjusted_after_dsicount' => $room_order['room_price'] - $room_order['discount'],

                'check_in' => $check_in,
                'check_out' => $check_out,
                'days' => count($room_orders ?? 0) ?? 0,

                "food_plan_id" => $food_plan_id,
                "food_plan_price" => $food_plan_price,
                "extra_bed_qty" => $extra_bed_qty,
                "bed_amount" => $bed_amount,

                "no_of_adult" => $no_of_adult,
                "no_of_child" => $no_of_child,

                "breakfast" => $breakfast,
                "lunch" => $lunch,
                "dinner" => $dinner,



                // $early_check_in = $request->early_check_in;
                // $late_check_out = $request->late_check_out;
                // $remaining_price = $request->remaining_price;

                // "early_check_in" => $early_check_in,
                // "late_check_out" => $late_check_out,
            ];
        }



        OrderRoom::insert($arr);

        unset($arr[0]["booked_room_id"]);
        unset($arr[0]["date"]);
        unset($arr[0]["price_adjusted_after_dsicount"]);

        BookedRoom::where('id', $id)->update($arr[0]);


        Booking::where("id", $booking_id)
            ->where("company_id", $company_id)
            ->update([
                'check_in' => $check_in,
                'check_out' => $check_out,
                'total_days' => $total_days,
                'user_id' => $user_id,
                'sub_total' => $booking_total_price,
                'total_price' => $booking_total_price,
                'all_room_Total_amount' => $booking_total_price,
                'balance' => $booking_remaining_price,
                'remaining_price' => $booking_remaining_price,
                'grand_remaining_price' => $booking_remaining_price,

                // 'discount' => $room_discount,    
                // 'after_discount' => $booking_total_price - $room_discount,
                // 'inv_total_tax_collected' => $bookingPayload->total_tax,
                // "rooms": "302,307",
                // "payment_status": 1,
                // "payment_mode_id": 1,
                // "": "18760",
                // "total_extra": 0,
            ]);


        Transaction::where("booking_id", $booking_id)->update([
            "balance" => $booking_total_price,
            "debit" => $booking_total_price,
            "user_id" => $user_id,
        ]);

        return $this->response('Booking has been modified.', null, true);
    }

    private function changeDateByDragProcess($bookingModel, $request, $bookedRoomId)
    {
        try {
            $bookedRoom = BookedRoom::find($bookedRoomId);
            $total_price = [];
            $orderRooms = [];
            $arr = [];
            $discount = 0;
            $iteration_date = [];
            $period = CarbonPeriod::create($bookingModel->check_out, $request->end);
            $numberOfNights = CarbonPeriod::create($bookingModel->check_in, $request->end);

            if (count($period) == 0) {
                $period = CarbonPeriod::create($bookingModel->check_in, $request->end);
                $dateArr = $period->toArray();
                $cancel_date = OrderRoom::whereNotIn('date', $dateArr)->whereBookedRoomId($bookedRoomId)->get()->toArray();
                OrderRoom::whereNotIn('date', $dateArr)->whereBookedRoomId($bookedRoomId)->delete();
                $total_cancel_amount = array_sum(array_column($cancel_date, 'grand_total'));
                $bookedRoom->update([
                    'check_in' => $request->start,
                    'check_out' => $request->end,

                    'room_tax' => $bookedRoom->room_tax - array_sum(array_column($cancel_date, 'room_tax')),
                    'price' => $bookedRoom->price - array_sum(array_column($cancel_date, 'price')),
                    'sgst' => $bookedRoom->sgst - array_sum(array_column($cancel_date, 'sgst')),
                    'cgst' => $bookedRoom->cgst - array_sum(array_column($cancel_date, 'cgst')),
                    'room_discount' => $bookedRoom->room_discount - array_sum(array_column($cancel_date, 'room_discount')),
                    'after_discount' => $bookedRoom->after_discount - array_sum(array_column($cancel_date, 'after_discount')),
                    'total' => $bookedRoom->total - array_sum(array_column($cancel_date, 'grand_total')),
                    'grand_total' => $bookedRoom->grand_total - array_sum(array_column($cancel_date, 'grand_total')),
                ]);
                return [
                    'extend_room_price' => -$total_cancel_amount,
                    'number_of_nights' => count($numberOfNights),
                ];
            } else {
                $period = CarbonPeriod::create($bookingModel->check_out, $request->end);
            }

            $room = Room::where('room_no', $request->roomId)->where('company_id', $bookingModel->company_id)->first();
            $prices = RoomType::whereCompanyId($bookingModel->company_id)->whereName($room->roomType->name)
                ->first(['holiday_price', 'weekend_price', 'weekday_price']);
            $orderRoomModel = OrderRoom::whereBookedRoomId($bookedRoom->id)->first()->toArray();
            $weekModel = Weekend::where('company_id', $bookingModel->company_id)->first();
            $weekends = $weekModel->day;
            $orderRooms_arr = [];
            foreach ($period as $date) {
                $orderRoomModel['date'] = $date->format('Y-m-d');
                OrderRoom::create($orderRoomModel);
                $orderRooms_arr[] = $orderRoomModel;
            }

            $bookedRoom->update([
                'check_in' => $request->start,
                'check_out' => $request->end,

                'room_tax' => $bookedRoom->room_tax + array_sum(array_column($orderRooms_arr, 'room_tax')),
                'price' => $bookedRoom->price + array_sum(array_column($orderRooms_arr, 'price')),
                'sgst' => $bookedRoom->sgst + array_sum(array_column($orderRooms_arr, 'sgst')),
                'cgst' => $bookedRoom->cgst + array_sum(array_column($orderRooms_arr, 'cgst')),
                'room_discount' => $bookedRoom->room_discount + array_sum(array_column($orderRooms_arr, 'room_discount')),
                'after_discount' => $bookedRoom->after_discount + array_sum(array_column($orderRooms_arr, 'after_discount')),
                'total' => $bookedRoom->total + array_sum(array_column($orderRooms_arr, 'grand_total')),
                'grand_total' => $bookedRoom->grand_total + array_sum(array_column($orderRooms_arr, 'grand_total')),
            ]);

            return [
                // 'extend_room_price' => array_sum($total_price),
                'extend_room_price' => array_sum(array_column($orderRooms_arr, 'grand_total')),
                'number_of_nights' => count($numberOfNights),
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

        $model->where('room_category_type', null);

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', "ILIKE", "%" . $request->source . "%");
        }

        if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_in', '>=', $request->from);
            $model->whereDate('check_in', '<=', $request->to);
        }

        if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_out', '>=', $request->from);
            $model->whereDate('check_out', '<=', $request->to);
        }
        if ($request->guest_mode == '' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_in', '>=', $request->from);
            $model->whereDate('check_in', '<=', $request->to);
        }

        $model->orderBy('id', 'desc');

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
                'bookedRooms:booking_id,id,room_no,room_type,booking_status',
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
        $model = Booking::query();
        //->latest();
        $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name,document',
            ]);

        $model->where('company_id', $request->company_id);

        if ($request->filled('status') && $request->status == 1) {
            $model->where('booking_status', $request->status);
            $model->WhereDate('check_in', $request->date);
        } elseif ($request->filled('status') && $request->status == 2) {
            $model->where('booking_status', $request->status);
            $model->whereDate('check_in', '<=', $request->date);
        } elseif ($request->filled('status') && $request->status == 3) {
            $model->where('booking_status', $request->status);
            $model->WhereDate('check_out', $request->date);
        }

        $model->where('booking_status', '!=', -1);
        $model->where('booking_status', '!=', 0);
        $model->where('booking_status', '<=', 2);

        //datatable filters
        if ($request->filled("reservation_no")) {
            $model->where('reservation_no', 'like', "$request->reservation_no%");
        }
        if ($request->filled('customer_name')) {
            $model->whereHas('customer', function ($q) use ($request) {
                $q->where('first_name', env("WILD_CARD") ?? 'ILIKE', "%$request->customer_name%");
            });
        }
        if ($request->filled('rooms')) {
            $model->where('rooms', env("WILD_CARD") ?? 'ILIKE', "%$request->rooms%");
        }
        if ($request->filled('check_in')) {
            $model->whereDate('check_in', $request->check_in);
        }
        if ($request->filled('check_out')) {
            $model->whereDate('check_out', $request->check_out);
        }
        if ($request->filled('booking_date')) {
            $model->whereDate('booking_date', $request->booking_date);
        }
        if ($request->filled('total_price')) {
            $model->where('total_price', '>=', $request->total_price);
        }
        if ($request->filled('source')) {
            $model->where('source', env("WILD_CARD") ?? 'ILIKE', "%$request->source%");
        }

        //datatable sorting

        if ($request->sortBy) {

            $sortDesc = $request->sortDesc == 'true' ? 'DESC' : 'ASC';
            if (strpos($request->sortBy, '.')) {
                $model->orderBy(Customer::select('first_name')->whereColumn('customers.id', 'bookings.customer_id'), $sortDesc);
            } else {
                $model->orderBy($request->sortBy, $sortDesc);
            }
        } else {
            $model->orderBy('created_at', 'DESC');
        }

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

    public function changeSingleRoom($oldRoom, $newRoom, $request)
    {
        $newRoomDetails = $this->getDataBySelectWithTax($oldRoom, $newRoom, $request);
        $newRoomEachDay = $newRoomDetails['data'];
        $totalNewRoomTax = array_sum(array_column($newRoomEachDay, 'tax'));
        $newRoomAmount = array_sum(array_column($newRoomEachDay, 'price'));
        $afterDiscountNewRoomAmount = $newRoomAmount - (float) $oldRoom->room_discount;
        $newRoomGrandAmount = $afterDiscountNewRoomAmount + $oldRoom->tot_adult_food + $oldRoom->tot_child_food;
        $numberOfDay = count($newRoomEachDay);
        $diff = $newRoomGrandAmount - (float) $oldRoom->grand_total;

        // return [
        //     'newRoomDetails'=> $newRoomDetails,
        //     'newRoomEachDay'=> $newRoomEachDay,
        //     'totalNewRoomAmountWithTax'=> $newRoomAmount,
        //     'totalNewRoomTax'=> array_sum(array_column($newRoomEachDay, 'tax')),
        //     'afterNewRoomDiscount'=>$afterDiscountNewRoomAmount,
        //     'newRoomGrandAmount'=>$newRoomGrandAmount,
        //     'oldRoomGrandTotal'=> $oldRoom->grand_total,
        //     'newRoomExtraAmount'=>$newRoomGrandAmount - (float)$oldRoom->grand_total,
        //     'numberOfDay'=> count($newRoomEachDay),
        //     'oldRoom'=> $oldRoom,
        // ];
        $oldRoomRoomNo = $oldRoom->room_no;
        $oldRoomCategory = $oldRoom->room_type;
        $newRoomNo = $newRoomDetails['room']['room_no'] ?? "";
        $newRoomCategory = $newRoomDetails['room']['room_type']['name'] ?? "";
        $oldRoom->room_id = $newRoomDetails['room']['id'] ?? "";
        $oldRoom->room_no = $newRoomNo ?? "";
        $oldRoom->room_type = $newRoomCategory ?? "";
        $oldRoom->price = $newRoomAmount;
        $oldRoom->after_discount = $afterDiscountNewRoomAmount;
        $oldRoom->check_in = $request->start;
        $oldRoom->check_out = $request->end;
        $oldRoom->cgst = $totalNewRoomTax / 2;
        $oldRoom->sgst = $totalNewRoomTax / 2;
        $oldRoom->room_tax = $totalNewRoomTax;
        $oldRoom->grand_total = $newRoomGrandAmount;
        $oldRoom->total = $newRoomGrandAmount;
        $oldRoom->save();

        // return $oldRoom;

        $arr = [
            'payment_mode_id' => 7,
            'user_id' => $request->user_id,
        ];
        $msg = "$oldRoomCategory room no $oldRoomRoomNo change to $newRoomCategory $newRoomNo";
        $booking = Booking::whereId($oldRoom->booking_id)->first();
        $this->updateTransactionByArr($booking, $arr, "$msg", 'debit', $diff);
        $this->updatePaymentByArr($booking, $arr, $diff, $msg);

        $orderRoomObj = OrderRoom::whereBookedRoomId($oldRoom->id)->first();
        $orderRoomDelete = OrderRoom::whereBookedRoomId($oldRoom->id)->delete();

        if ($orderRoomDelete) {
            foreach ($newRoomEachDay as $singleDay) {
                $orderRoomObj->date = $singleDay['date'];
                $orderRoomObj->room_no = $newRoomDetails['room']['room_no'];
                $orderRoomObj->room_type = $newRoomDetails['room']['room_type']['name'];
                $orderRoomObj->price = $singleDay['price'];
                $orderRoomObj->room_tax = $singleDay['tax'];
                $orderRoomObj->sgst = $singleDay['tax'] / 2;
                $orderRoomObj->cgst = $singleDay['tax'] / 2;
                $orderRoomObj->grand_total = $newRoomGrandAmount / $numberOfDay;
                $orderRoomObj->total_with_discount = $afterDiscountNewRoomAmount / $numberOfDay;
                $orderRoomObj->after_discount = $afterDiscountNewRoomAmount / $numberOfDay;
                $orderRoomObj->total = $newRoomGrandAmount / $numberOfDay;
                $orderRoomObj->total_with_tax = $afterDiscountNewRoomAmount / $numberOfDay;
                OrderRoom::create($orderRoomObj->toArray());
            }
        }

        return true;

        return [
            'newRoomDetails' => $newRoomDetails,
            'oldRoom' => $oldRoom,
        ];
    }

    private function updateTransactionByArr($booking, $arr, $desc = "", $mode, $amt)
    {
        $transactionData = [
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id ?? '',
            'date' => now(),
            'company_id' => $booking->company_id ?? '',
            'payment_method_id' => $arr['payment_mode_id'],
            'desc' => $desc,
            'reference_number' => $arr['reference_number'] ?? "",
            'user_id' => $arr['user_id'],
        ];
        (new TransactionController())->store($transactionData, $amt, $mode);
        (new TransactionController())->updateBookingByTransactions($booking->id, 0);
    }

    private function updatePaymentByArr($booking, $arr, $amt, $desc = "")
    {
        $payment = Payment::whereBookingId($booking->id)->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
        if ($payment) {
            $payment->amount = (float) $payment->amount + (float) $amt;
            $payment->save();
        }
    }

    public function getDataBySelectWithTax($oldRoom, $newRoom, $request)
    {


        // dd($newRoom->roomType->name);
        $company_id = $request->company_id;
        $discount = $request->discount ?? 0;
        $room = Room::where('room_no', $newRoom->room_no)->where('company_id', $request->company_id)->first();
        $prices = RoomType::whereCompanyId($request->company_id)->whereName($newRoom->roomType->name)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends = $weekModel->day;

        $arr = [];
        $period = CarbonPeriod::create($request->start, $this->checkOutDate($request->end));
        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day = $date->format('D');
            $isWeekend = in_array($day, $weekends);
            $isHoliday = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->holiday_price - $discount, $request->company_id)['total_with_tax'],
                    "day_type" => "holiday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->holiday_price - $discount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekend_price - $discount, $request->company_id)['total_with_tax'],
                    "tax" => $this->getRoomTax($prices->weekend_price - $discount, $request->company_id)['room_tax'],
                    "day_type" => "weekend",
                    "day" => $day,
                    "room_price" => $prices->weekend_price,
                ];
            } else {
                $arr[] = [
                    "date" => $iteration_date,
                    "price" => $this->getRoomTax($prices->weekday_price - $discount, $request->company_id)['total_with_tax'],
                    "day_type" => "weekday",
                    "day" => $day,
                    "tax" => $this->getRoomTax($prices->weekday_price - $discount, $request->company_id)['room_tax'],
                    "room_price" => $prices->weekday_price,
                ];
            }
        }

        return [
            'room' => $room,
            'data' => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
            'total_tax' => array_sum(array_column($arr, "tax")),
        ];

        return Room::where('room_no', $request->room_no)
            ->where('status', 0)
            ->where('company_id', $request->company_id)
            ->first();
    }

    public function checkHoliday($date, $company_id)
    {
        return Holiday::where(function ($q) use ($date) {
            $q->where('from', '<=', $date);
            $q->where('to', '>=', $date);
        })->whereCompanyId($company_id)->exists();
    }




    public function groupBooking(Request $request)
    {

        $diff_in_seconds = strtotime($request->check_in) - strtotime(date('Y-m-d'));
        if ($diff_in_seconds < 0) {
            return response()->json(['data' => 'Booking Date is invalid', 'status' => false]);
        }

        $booking = null;

        //verify is booking  availalbe with date and room number

        $bookedRoomsCount = BookedRoom::whereDate('check_in', '<=', $request->check_in)
            ->WhereDate('check_out', '>=', $request->check_out)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
                $q->where('room_id', $request->selectedRooms[0]['room_id']);
            })->count();



        if ($bookedRoomsCount > 0) {
            return response()->json(['error' => 'Room is not availalbe on this Date']); // return a user-friendly error 
        }


        DB::beginTransaction();
        try {
            $customer_id = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            //$booking = $this->storeBooking($request);

            $bookingArray = $this->storeGroupBooking($request);
            $booking_reservation_number =  $bookingArray[1];
            $booking  =  $bookingArray[0];



            if ($booking) {
                $this->storeBookedRooms($request, $booking);
                //recalculating Tax based on discount 
                (new ManagementController())->generateOccupancyRateByBooking($request);
                (new RecalculateTaxController())->UpdateTaxWithID($booking->id);

                if ($request->filled("payment_reference_id")) {
                    $data = [];
                    $data['payment_reference_id'] = $request->payment_reference_id;
                    $data['payment_response'] =  json_encode($request->payment_response);

                    Booking::whereId($booking->id)->update($data);
                }
            }
            DB::commit();
            return response()->json(['data' => $booking->id, 'booking_reservation_number' => $booking_reservation_number, 'status' => true]);

            // all good
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'An error occurred. Please try again.' . $e->getMessage()]); // return a user-friendly error 
        }
    }

    public function storeGroupBooking($request)
    {
        //try {
        //return DB::transaction(function () use ($request) {


        $data = [];
        $data = $request->only(Booking::bookingAttributes());
        $data['booking_date'] = now();
        $data['payment_status'] = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
        $data['remaining_price'] = (float) $request->total_price - (float) $request->advance_price;
        $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
        $data['reservation_no'] = $this->getReservationNumber($data);
        $data['verified'] = Booking::VERIFICATION_REQUIRED;

        if ($request->filled('api_json_reference_number')) {
            $data['widget_confirmation_number'] = $request->api_json_reference_number;
        }

        $booked = Booking::create($data);

        if ($booked) {

            $transactionData = [
                'booking_id' => $booked->id,
                'customer_id' => $booked->customer_id ?? '',
                'date' => now(),
                'company_id' => $request->company_id ?? '',
                'desc' => 'rooms booking amount',
                'reference_number' => $request->reference_number,
                'payment_method_id' => 7,
                'user_id' => $request->user_id,
            ];

            //Transaction
            $payment = new TransactionController();
            $payment->store($transactionData, $request->total_price, 'debit');

            if ($request->advance_price && $request->advance_price > 0) {
                $transactionData['desc'] = 'advance payment';
                $transactionData['payment_method_id'] = $booked->payment_mode_id;

                $payment->store($transactionData, $request->advance_price, 'credit');
            }
            //End Transaction
            if ((float) $booked->advance_price == 0) {

                if (($booked->paid_by && $booked->paid_by == 2) || ($booked->type != 'Walking' && $booked->type != 'Complimentary')) {

                    $agentsData = [
                        'booking_id' => $booked->id,
                        'customer_id' => $booked->customer_id ?? '',
                        'type' => $booked->type ?? '',
                        'source' => $booked->source,
                        'reference_no' => $booked->reference_no ?? '',
                        'amount' => $booked->total_price ?? '',
                        'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                        'company_id' => $request->company_id ?? '',
                        'is_paid' => $booked->paid_by == 1 ? 2 : 0,
                    ];
                    $payment = new AgentsController();
                    $payment->store($agentsData);

                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => 7,
                        'description' => $booked->source,
                        'amount' => $booked->remaining_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 1,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                } else {
                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => 7,
                        'description' => $booked->source,
                        'amount' => $booked->remaining_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 1,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }
            } else {

                if ($request->total_price >= $request->advance_price) {

                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => $booked->payment_mode_id,
                        'description' => 'advance payment',
                        'amount' => $booked->advance_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 0,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }

                $paymentsData = [
                    'booking_id' => $booked->id,
                    'payment_mode' => 7,
                    'description' => 'pending payment',
                    'amount' => $booked->remaining_price,
                    'type' => 'room',
                    'room' => $booked->rooms,
                    'company_id' => $request->company_id,
                    'is_city_ledger' => 1,
                ];
                $payment = new PaymentController();
                $payment->store($paymentsData);

                $agentsData = [
                    'booking_id' => $booked->id,
                    'customer_id' => $booked->customer_id ?? '',
                    'type' => 'Customer' ?? '',
                    'source' => $booked->source,
                    'reference_no' => $booked->reference_no ?? '',
                    'amount' => $booked->total_price ?? '',
                    'agent_paid_amount' => $booked->advance_price ?? '',
                    'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                    'company_id' => $request->company_id ?? '',
                ];
                $payment = new AgentsController();
                $payment->store($agentsData);
            }

            if ($request->gst_number) {
                (new TaxableController())->storeTaxableInvoice($booked);
            }
        }

        return [$booked, $data['reservation_no']];

        return $this->response('Room Booked Successfully.', $booked, true);
        // });
        // } catch (\Throwable $th) {
        //     return $th;
        //     Logger::channel("custom")->error("BookingController: " . $th);
        //     return ["done" => false, "data" => "DataBase Error booking"];
        // }
    }





    public function hallBooking(Request $request)
    {

        // $diff_in_seconds = strtotime($request->check_in) - strtotime(date('Y-m-d'));
        // if ($diff_in_seconds < 0) {
        //     return response()->json(['data' => 'Booking Date is invalid', 'status' => false]);
        // }

        $booking = null;

        //verify is booking  availalbe with date and room number

        $bookedRoomsCount = BookedRoom::whereDate('check_in', '<=', $request->check_in)
            ->WhereDate('check_out', '>=', $request->check_out)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_status', '!=', 0);
                $q->where('company_id', $request->company_id);
                $q->where('room_id', $request->selectedRooms[0]['room_id']);
            })->count();



        if ($bookedRoomsCount > 0) {
            return response()->json(['error' => 'Room is not availalbe on this Date']); // return a user-friendly error 
        }


        DB::beginTransaction();
        try {
            $customer_id = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            //$booking = $this->storeBooking($request);

            $bookingArray = $this->storeGroupBooking($request);
            $booking_reservation_number =  $bookingArray[1];
            $booking  =  $bookingArray[0];



            if ($booking) {
                $this->storeBookedRoomsForHall($request, $booking);
                //recalculating Tax based on discount 
                (new ManagementController())->generateOccupancyRateByBooking($request);
                (new RecalculateTaxController())->UpdateTaxWithID($booking->id);

                if ($request->filled("payment_reference_id")) {
                    $data = [];
                    $data['payment_reference_id'] = $request->payment_reference_id;
                    $data['payment_response'] =  json_encode($request->payment_response);

                    Booking::whereId($booking->id)->update($data);
                }
            }
            DB::commit();
            return response()->json(['data' => $booking->id, 'booking_reservation_number' => $booking_reservation_number, 'status' => true]);

            // all good
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'An error occurred. Please try again.' . $e->getMessage()]); // return a user-friendly error 
        }
    }


    public function storeBookedRoomsForHall($request, $booking)
    {
        try {
            $rooms = $request->only('selectedRooms');

            foreach ($rooms['selectedRooms'] as $room) {

                $room['booking_id'] = $booking->id;
                $room['customer_id'] = $booking->customer_id;
                $room['booking_status'] = $booking->booking_status;

                $room['check_in'] = $booking->check_in;
                $room['check_out'] = $booking->check_out;



                $priceList = $room['priceList'];

                unset($room['priceList']);
                unset($room['meal_name']);

                unset($room['extra_hours_charges']);


                unset($room['check_in_time']);
                unset($room['check_out_time']);

                $bookedRoomId = BookedRoom::create($room);

                $orderRooms = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));
                $singleDayDiscount = ($room['room_discount'] / count($priceList));
                $singleDayExtraAmount = ($room['room_extra_amount'] / count($priceList));
                // $singleDayPrice = ($room['price'] / count($priceList));

                foreach ($priceList as $list) {
                    $singleDayPrice = $list['room_price'];
                    // Recalculation start
                    $taxArray = $this->reCalculatePrice($list['price'] - $singleDayDiscount + $singleDayExtraAmount);

                    $price_adjusted_after_dsicount = $taxArray['basePrice'];
                    $list['tax'] = $taxArray['gstAmount'];
                    // Recalculation end

                    $orderRooms['price_adjusted_after_dsicount'] = $price_adjusted_after_dsicount;
                    $orderRooms['date'] = $list['date'];

                    $orderRooms['room_discount'] = $singleDayDiscount;
                    $orderRooms['after_discount'] = $list['price'] - $orderRooms['room_discount'] + $singleDayExtraAmount;

                    $orderRooms['price'] = $singleDayPrice;

                    $orderRooms['total_with_tax'] = $orderRooms['after_discount'];

                    $orderRooms['total'] = $orderRooms['total_with_tax'];
                    $orderRooms['grand_total'] = $orderRooms['total_with_tax'];

                    $orderRooms['days'] = 1;
                    $orderRooms['room_tax'] = $list['tax'];
                    $orderRooms['sgst'] = $list['tax'] / 2;
                    $orderRooms['cgst'] = $list['tax'] / 2;
                    $orderRooms['booked_room_id'] = $bookedRoomId->id;
                    $orderRooms['customer_id'] = $bookedRoomId->customer_id;
                    $orderRooms['meal'] = $bookedRoomId->meal;
                    $orderRooms['no_of_adult'] = $bookedRoomId->no_of_adult;
                    $orderRooms['no_of_child'] = $bookedRoomId->no_of_child;
                    $orderRooms['no_of_baby'] = $bookedRoomId->no_of_baby;
                    $orderRooms['food_plan_id'] = $bookedRoomId->food_plan_id;
                    $orderRooms['food_plan_price'] = $bookedRoomId->food_plan_price;
                    $orderRooms['early_check_in'] = $bookedRoomId->early_check_in;
                    $orderRooms['late_check_out'] = $bookedRoomId->late_check_out;

                    $orderRooms['cleaning'] = $bookedRoomId->cleaning;
                    $orderRooms['electricity'] = $bookedRoomId->electricity;
                    $orderRooms['generator'] = $bookedRoomId->generator;
                    $orderRooms['audio'] = $bookedRoomId->audio;
                    $orderRooms['projector'] = $bookedRoomId->projector;


                    $orderRooms['hall_min_hours'] = $bookedRoomId->hall_min_hours;
                    $orderRooms['extra_hours'] = $bookedRoomId->extra_hours;
                    $orderRooms['total_booking_hours'] = $bookedRoomId->total_booking_hours;
                    $orderRooms['extra_booking_hours_charges'] = $bookedRoomId->extra_booking_hours_charges;

                    $orderRooms['extra_bed_qty '] = 0;


                    OrderRoom::create($orderRooms);
                }
            }

            if (app()->isProduction()) {
                $customer = Customer::find($booking->customer_id);
                (new WhatsappNotificationController())->whatsappNotification($booking, $rooms['selectedRooms'], $customer, 'booking');
            }

            return $rooms;
            return $this->response('Room Booked Successfully.', $rooms, true);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function  getRoomStatusColorCode($status)

    {
        $colors = (new BookingController())->RoomColorCodes();
        $index = array_search($status, array_column($colors, 'status_id'));
        return     $index !== false ? $colors[$index]['color'] : null;
    }
    public function  RoomColorCodes()
    {
        return [
            ['status_id' => 0, 'color' => '#4caf50', "desc" => "avaialbe"],
            ['status_id' => 1, 'color' => '#538234', "desc" => "booked"],
            ['status_id' => 2, 'color' => '#0f642b', "desc" => "checkedin"],
            ['status_id' => 3, 'color' => '#fb0103', "desc" => "checkedout/dirty"],
            ['status_id' => 4, 'color' => '#92d051', "desc" => "expected_arrival"],
            ['status_id' => 5, 'color' => '#92d051', "desc" => "avaialbe"],
            ['status_id' => 6, 'color' => '#8faadd', "desc" => "booking done-nopayment"],
            ['status_id' => 7, 'color' => '#c55a12', "desc" => "expected checkout"],
            ['status_id' => 8, 'color' => '#702fa5', "desc" => "checkedout but due payment"],






            ['status_id' => 11, 'color' => '#04b0f2', "desc" => "OTA Online Travel Agency"],
            ['status_id' => 12, 'color' => '#7551e5', "desc" => "Walkin customer "],
            ['status_id' => 13, 'color' => '#ff00dc', "desc" => "Travel agent"],
            ['status_id' => 14, 'color' => '#010002', "desc" => "Compliment"],
            ['status_id' => 15, 'color' => '#c65a12', "desc" => "Corporate"],


        ];
    }
}
