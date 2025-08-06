<?php
namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingRequest;
use App\Http\Requests\Booking\DocumentRequest;
use App\Jobs\EmailSender;
use App\Jobs\StoreBookedRoomsJob;
use App\Jobs\StoreBookedRoomsJobForDirectCheckIn;
use App\Jobs\WhatsappSender;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Food;
use App\Models\HallBookings;
use App\Models\Holiday;
use App\Models\IdCardType;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\PostingPayment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Source;
use App\Models\SubCustomer;
use App\Models\SubCustomerRoomHistory;
use App\Models\TaxSlabs;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\Weekend;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;

class BookingController extends Controller
{
    public function groupList(Request $request)
    {
        return [];
        return Booking::with(["customer", "room"])
            ->whereNotNull("group_name")
            ->where('company_id', $request->company_id)
            ->where('booking_status', '!=', 0)
            ->orderByDesc("group_name")
            ->get();
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
    public function getCheckedInRoomList()
    {
        return BookedRoom::where('booking_id', request('booking_id'))
            ->where('booking_status', BookedRoom::CHECKED_IN)
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

    public function getNotificationCount(Request $request)
    {

        $model = Booking::query();
        $model->where('company_id', $request->company_id);

        $model->whereNot('widget_confirmation_number', null);
        $model->where('booking_date', "=", date('Y-m-d'));

        $data['online_booking_count'] = $model->pluck("id");

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
            $customer_id            = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            //$booking = $this->storeBooking($request);

            $bookingArray               = $this->storeBooking($request);
            $booking_reservation_number = $bookingArray[1];
            $booking                    = $bookingArray[0];

            if ($booking) {
                $error = $this->storeBookedRooms($request, $booking);

                //DB::commit();

                //recalculating Tax based on discount
                $error = (new ManagementController())->generateOccupancyRateByBooking($request);

                try {

                    if ($request->filled("payment_reference_id")) {
                        $data                         = [];
                        $data['payment_reference_id'] = $request->payment_reference_id;
                        $data['payment_response']     = json_encode($request->payment_response);

                        Booking::whereId($booking->id)->update($data);
                    }
                } catch (\Exception $e) {
                }
            } else {
            }

            $this->processNotification(Template::BOOKING_CREATE, "Booking", $request);
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

        $merge_food_in_room_price         = (int) $request->merge_food_in_room_price;
        $data                             = [];
        $data                             = $request->only(Booking::bookingAttributes());
        $data['booking_date']             = date("Y-m-d");
        $data['merge_food_in_room_price'] = $merge_food_in_room_price;
        $data['payment_status']           = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
        $data['remaining_price']          = (float) $request->total_price - (float) $request->advance_price;
        $data['grand_remaining_price']    = (int) $request->total_price - (float) $request->advance_price;
        $data['reservation_no']           = $this->getReservationNumber($data);

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
                    'baby'  => array_sum(array_column(array_column($arr, 'breakfast'), 'baby')),
                ],
                'lunch'     => [
                    'adult' => array_sum(array_column(array_column($arr, 'lunch'), 'adult')),
                    'child' => array_sum(array_column(array_column($arr, 'lunch'), 'child')),
                    'baby'  => array_sum(array_column(array_column($arr, 'lunch'), 'baby')),
                ],
                'dinner'    => [
                    'adult' => array_sum(array_column(array_column($arr, 'dinner'), 'adult')),
                    'child' => array_sum(array_column(array_column($arr, 'dinner'), 'child')),
                    'baby'  => array_sum(array_column(array_column($arr, 'dinner'), 'baby')),
                ],
            ];

            Food::create([
                'booking_id' => $booked->id,
                'breakfast'  => $final_arr['breakfast'],
                'lunch'      => $final_arr['lunch'],
                'dinner'     => $final_arr['dinner'],
                'company_id' => $request->company_id,
            ]);

            $transactionData = [
                'booking_id'        => $booked->id,
                'customer_id'       => $booked->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $request->company_id ?? '',
                'desc'              => 'rooms booking amount',
                'reference_number'  => $request->reference_number,
                'payment_method_id' => 7,
                'user_id'           => $request->user_id,
            ];

            //Transaction
            $payment = new TransactionController();
            $payment->store($transactionData, $request->total_price, 'debit');

            if ($request->advance_price && $request->advance_price > 0) {
                $transactionData['desc']              = 'payment';
                $transactionData['payment_method_id'] = $booked->payment_mode_id;

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
                        'description'    => $booked->source,
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
                        'description'    => $booked->source,
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
        return Booking::orderBy('id', 'desc')->where('company_id', $company_id)->value("reservation_no") + 1 ?? 1000;

        $starting_value = 00001;
        $model          = Booking::query();

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

                $room['booking_id']     = $booking->id;
                $room['customer_id']    = $booking->customer_id;
                $room['booking_status'] = $booking->booking_status;

                $priceList = $room['priceList'];

                unset($room['priceList']);
                unset($room['meal_name']);
                unset($room['total_price']);
                unset($room['room_type_object']);

                $bookedRoomId         = BookedRoom::create($room);
                $orderRooms           = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));
                $singleDayDiscount    = ($request->room_discount / count($priceList) / count($rooms['selectedRooms']));
                $singleDayExtraAmount = ($request->room_extra_amount / count($priceList) / count($rooms['selectedRooms']));
                // $singleDayPrice = ($room['price'] / count($priceList));

                foreach ($priceList as $list) {
                    $singleDayPrice = $list['room_price'];
                    // Recalculation start
                    $taxArray = $this->reCalculatePrice($list['price'] - $singleDayDiscount + $singleDayExtraAmount);

                    $price_adjusted_after_dsicount = $taxArray['basePrice'];
                    $list['tax']                   = $taxArray['gstAmount'];
                    // Recalculation end

                    $orderRooms['price_adjusted_after_dsicount'] = $price_adjusted_after_dsicount;
                    $orderRooms['date']                          = $list['date'];

                    $orderRooms['room_discount']  = $singleDayDiscount;
                    $orderRooms['after_discount'] = ($list['price'] - $orderRooms['room_discount']) + $singleDayExtraAmount;

                    $price = $orderRooms['after_discount'];

                    $orderRooms['total']       = $price + $bookedRoomId->food_plan_price;
                    $orderRooms['grand_total'] = $price + $bookedRoomId->food_plan_price;

                    $orderRooms['total_with_tax'] = $price;

                    $orderRooms['price'] = $list['price'];

                    $orderRooms['days']            = 1;
                    $orderRooms['room_tax']        = $list['tax'];
                    $orderRooms['sgst']            = $list['tax'] / 2;
                    $orderRooms['cgst']            = $list['tax'] / 2;
                    $orderRooms['booked_room_id']  = $bookedRoomId->id;
                    $orderRooms['customer_id']     = $bookedRoomId->customer_id;
                    $orderRooms['meal']            = $bookedRoomId->meal;
                    $orderRooms['no_of_adult']     = $bookedRoomId->no_of_adult;
                    $orderRooms['no_of_child']     = $bookedRoomId->no_of_child;
                    $orderRooms['no_of_baby']      = $bookedRoomId->no_of_baby;
                    $orderRooms['food_plan_id']    = $bookedRoomId->food_plan_id;
                    $orderRooms['food_plan_price'] = $bookedRoomId->food_plan_price;
                    $orderRooms['extra_bed_qty']   = $bookedRoomId->extra_bed_qty;
                    $orderRooms['early_check_in']  = $bookedRoomId->early_check_in;
                    $orderRooms['late_check_out']  = $bookedRoomId->late_check_out;

                    $orderRooms['breakfast'] = $bookedRoomId->breakfast ?? 0;
                    $orderRooms['lunch']     = $bookedRoomId->lunch ?? 0;
                    $orderRooms['dinner']    = $bookedRoomId->dinner ?? 0;

                    $orderRooms['tariff'] = $list['day_type'] ?? "";
                    $orderRooms['day']    = $list['day'] ?? null;

                    OrderRoom::create($orderRooms);
                }
            }

            // if (app()->isProduction()) {
            //     $customer = Customer::find($booking->customer_id);
            //     (new WhatsappNotificationController())->whatsappNotification($booking, $rooms['selectedRooms'], $customer, 'booking');
            // }

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
        $tax               = 12;
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
        $tax                     = 12; //default

        $calculationStatus = false;
        $tax               = 12;
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

        $booking  = Booking::find($request->booking_id);
        $customer = Customer::find($booking->customer_id);
        if ($request->hasFile('document')) {
            $file = $request->file('document');

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

    public function storeDocumentTest(Request $request)
    {

        // return $request->all();

        if ($request->hasFile('document')) {
            $file     = $request->file('document');
            $ext      = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path     = $file->storeAs('public/test/doc', $fileName);
        }

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $ext      = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $path     = $file->storeAs('public/test/img', $fileName);
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
            'booking_id'        => $booking->id,
            'customer_id'       => $booking->customer_id ?? '',
            'date'              => now(),
            'company_id'        => $booking->company_id ?? '',
            'payment_method_id' => $request->payment_mode_id,
            'desc'              => $desc,
            'reference_number'  => $request->reference_number,
            'user_id'           => $request->user_id,
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
            $existingCustomer = $this->getExistingCustomer($customer);

            if ($existingCustomer) {
                $existingCustomer->update($customer);
                return $existingCustomer->id;
            }

            // $customer = $this->handleFileUploads($customer);

            $newCustomer = Customer::create($customer);

            return $newCustomer->id;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function getExistingCustomer($customer)
    {
        if (! empty($customer['contact_no'])) {
            return Customer::where('contact_no', $customer['contact_no'])
                ->where('company_id', $customer['company_id'])
                ->first();
        }

        return null;
    }

    private function handleFileUploads($customer)
    {
        $fileKeys = ['id_frontend_side', 'id_backend_side', 'captured_photo', 'sign'];

        foreach ($fileKeys as $key) {
            if ($url = request($key)) {
                $customer[$key] = $this->processFile($url, $key);
            }
        }

        return $customer;
    }

    public function processFile($url, $prefix = 'file')
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                // Fetch the image from the URL
                $imageContents = file_get_contents($url);

                // Generate a unique image name
                $imageName = "$prefix-" . time() . ".png";

                // Define the storage path
                $publicDirectory = public_path("customer_id_pic");

                // Ensure the directory exists
                if (! file_exists($publicDirectory)) {
                    mkdir($publicDirectory, 0777, true);
                }

                // Save the image
                file_put_contents($publicDirectory . '/' . $imageName, $imageContents);

                // Store the image name in the database
                return $imageName;
            } catch (\Exception $e) {
                // Handle errors (e.g., invalid URL, failed download, etc.)
                return response()->json(['error' => 'Unable to process the image URL.'], 400);
            }
        } else {
            return response()->json(['error' => 'Invalid URL provided.'], 400);
        }
    }

    public function check_in_room(Request $request)
    {
        DB::beginTransaction();

        try {

            // session(['isCheckoutSes' => true]);

            $company_id = $request->company_id;
            $booking_id = $request->booking_id;
            $room_id    = $request->room_id;
            $booking    = Booking::find($booking_id);

            $title     = null;
            $full_name = null;
            $whatsapp  = null;
            $email     = null;

            if ($request->filled('guest')) {
                $validatedData = $request->validate([
                    'guest.title'       => 'required|string|max:10',
                    'guest.first_name'  => 'required|string|max:50',
                    'guest.last_name'   => 'required|string|max:50',
                    'guest.contact_no'  => 'required|string|max:15',
                    'guest.whatsapp'    => 'nullable|string|max:15',
                    'guest.email'       => 'required|max:100',
                    'guest.dob'         => 'nullable|date',
                    'guest.nationality' => 'required|string|max:50',
                    'guest.city'        => 'required|string|max:50',
                    'guest.state'       => 'required|string|max:50',
                    'guest.country'     => 'required|string|max:50',
                    'guest.zip_code'    => 'nullable|string|max:50',
                ]);

                if ($validatedData) {
                    $guest                = $validatedData["guest"];
                    $guest["customer_id"] = $booking->customer_id;

                    $title      = $guest["title"] ?? "";
                    $first_name = $guest["first_name"] ?? "";
                    $last_name  = $guest["last_name"] ?? "";
                    $full_name  = trim($first_name . " " . $last_name);

                    $whatsapp = $guest["whatsapp"] ?? "";
                    $email    = $guest["email"] ?? "";

                    $subCustomer = SubCustomer::create($guest);

                    SubCustomerRoomHistory::create([
                        "room_id"         => $room_id,
                        "sub_customer_id" => $subCustomer->id,
                    ]);

                    PostingPayment::create([
                        "booking_id"      => $booking_id,
                        "room_id"         => $room_id,
                        "sub_customer_id" => $subCustomer->id,
                    ]);
                }
            } else {
                $customer = $request->customer;

                $arr = [];

                if ($customer) {
                    if ($customer['first_name']) {
                        $arr["first_name"] = $customer['first_name'];
                    }

                    if ($customer['last_name']) {
                        $arr["last_name"] = $customer['last_name'];
                    }

                    if ($customer['contact_no']) {
                        $arr["contact_no"] = $customer['contact_no'];
                    }

                    if ($customer['whatsapp']) {
                        $arr["whatsapp"] = $customer['whatsapp'];
                    }

                    if ($customer['email']) {
                        $arr["email"] = $customer['email'];
                    }

                    if ($customer['car_no']) {
                        $arr["car_no"] = $customer['car_no'];
                    }

                    if ($customer['no_of_adult']) {
                        $arr["no_of_adult"] = $customer['no_of_adult'];
                    }

                    if ($customer['no_of_child']) {
                        $arr["no_of_child"] = $customer['no_of_child'];
                    }

                    if ($customer['no_of_baby']) {
                        $arr["no_of_baby"] = $customer['no_of_baby'];
                    }

                    if ($customer['address']) {
                        $arr["address"] = $customer['address'];
                    }

                    if ($customer['customer_type']) {
                        $arr["customer_type"] = $customer['customer_type'];
                    }

                    if ($customer['dob']) {
                        $arr["dob"] = $customer['dob'];
                    }

                    if ($customer['title']) {
                        $arr["title"] = $customer['title'];
                    }

                    if ($customer['nationality']) {
                        $arr["nationality"] = $customer['nationality'];
                    }

                    if ($customer['gst_number']) {
                        $arr["gst_number"] = $customer['gst_number'];
                    }

                    if ($customer['id_frontend_side']) {
                        $arr["id_frontend_side"] = $customer['id_frontend_side'];
                    }

                    if ($customer['id_backend_side']) {
                        $arr["id_backend_side"] = $customer['id_backend_side'];
                    }

                    if ($customer['captured_photo']) {
                        $arr["captured_photo"] = $customer['captured_photo'];
                    }

                    if ($customer['sign']) {
                        $arr["sign"] = $customer['sign'];
                    }

                    if ($customer['country']) {
                        $arr["country"] = $customer['country'];
                    }

                    if ($customer['state']) {
                        $arr["state"] = $customer['state'];
                    }

                    if ($customer['city']) {
                        $arr["city"] = $customer['city'];
                    }

                    if ($customer['zip_code']) {
                        $arr["zip_code"] = $customer['zip_code'];
                    }

                    if ($customer['source_id']) {
                        $arr["source_id"] = $customer['source_id'];
                    }

                    Customer::where("id", $customer["id"])->update($arr);

                    $title      = $arr["title"] ?? "";
                    $first_name = $arr["first_name"] ?? "";
                    $last_name  = $arr["last_name"] ?? "";
                    $full_name  = trim($first_name . " " . $last_name);
                    $whatsapp   = $arr["whatsapp"];
                    $email      = $arr["email"];
                }
            }

            if ($request->discount > 0) {
                $this->updateTransaction($booking, $request, 'discount', 'debit', -abs($request->discount));
                $bookedRoom = BookedRoom::whereBookingId($booking_id)->first();
                $bookedRoom->increment('room_discount', $request->discount);
            }

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $request->payment_mode_id,
                'desc'              => 'check in payment',
                'reference_number'  => $request->reference_number,
                'user_id'           => $request->user_id,
            ];

            $trans = new TransactionController();

            if ($request->isHall && $request->exceedHoursCharges > 0) {
                $transactionData["desc"] = "additional hours charges";
                $trans->store($transactionData, $request->exceedHoursCharges ?? 0, 'debit');
            }
            if ($request->full_payment > 0) {
                $trans->store($transactionData, $request->full_payment ?? 0, 'credit');
            }

            if ($booking->balance > 0) {
                $booking->payment_status        = 0;
                $booking->remaining_price       = (int) $booking->remaining_price - (int) $request->full_payment;
                $booking->grand_remaining_price = (int) $booking->remaining_price + (int) $booking->total_posting_amount;
            } else {
                $booking->payment_status        = 1;
                $booking->full_payment          = $booking->paid_amounts;
                $booking->remaining_price       = 0;
                $booking->grand_remaining_price = 0;
                $booking->total_posting_amount  = 0;
            }

            $booking->booking_status = 2;
            $booking->save();

            $paymentsData = [
                'booking_id'     => $booking_id,
                'payment_mode'   => $request->payment_mode_id,
                'description'    => 'check in payment',
                'amount'         => $request->full_payment,
                'type'           => 'customer',
                'room'           => $booking->rooms,
                'company_id'     => $booking->company_id,
                'is_city_ledger' => 0,
                'created_at'     => now(),
            ];
            if ($request->full_payment > 0) {
                $payment = Payment::whereBookingId($booking->id)
                    ->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                if ($payment) {
                    $payment->amount = (int) $booking->balance;
                    $payment->save();
                }
                (new PaymentController())->store($paymentsData);
            }

            BookedRoom::where(["booking_id" => $booking_id, "room_id" => $room_id])
                ->update([
                    "booking_status"        => BookedRoom::CHECKED_IN,
                    "room_status"           => BookedRoom::CHECKED_IN,
                    "actual_check_in_time"  => date('H:i'),
                    "actual_check_out_time" => "---",
                ]);

            $payload = [
                "command"    => Template::WHEN_CUSTOMER_ARRIVED,
                "heading"    => "WHEN CUSTOMER ARRIVED",
                "company_id" => $company_id,
                "whatsapp"   => $whatsapp,
                "email"      => $email,

                "fields"     => [
                    "title"      => $title,
                    "full_name"  => $full_name,
                    "company_id" => $company_id,
                ],
            ];

            if ($payload["whatsapp"]) {

                $whatsappPayload = [
                    'recipient'  => $payload["whatsapp"],
                    'text'       => (new Controller)->prepareMessage($payload['fields'], "whatsapp", $payload["command"]),
                    'company_id' => $company_id,
                ];

                WhatsappSender::dispatch($whatsappPayload);
            }

            if ($payload["email"]) {

                $emailPayload = [
                    'recipient'  => $payload["email"],
                    'text'       => (new Controller)->prepareMessage($payload['fields'], "email", $payload["command"]),
                    'company_id' => $company_id,
                    "heading"    => $payload["heading"],
                ];

                // Mail::to($payload["email"])->queue(new EmailDispatcherWithAttachment($emailPayload));
                EmailSender::dispatch($emailPayload);

            }
            DB::commit();

            return response()
                ->json(['bookingId' => $booking_id, 'message' => 'Checked In Successfully', 'status' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Checkin failed', 'status' => false], 500);

        }
    }

    public function direct_check_in_room(Request $request)
    {
        try {
            $id      = $request->booking_id ?? 0;
            $room_id = $request->room_id ?? 0;

            Booking::where("id", $id)->update(['booking_status' => BookedRoom::CHECKED_IN]);

            BookedRoom::where("booking_id", $id ?? 0)
                ->where("room_id", $room_id)
                ->update([
                    'check_in'              => date('Y-m-d'),
                    "booking_status"        => BookedRoom::CHECKED_IN,
                    "room_status"           => BookedRoom::CHECKED_IN,
                    "actual_check_in_time"  => date('H:i'),
                    "actual_check_out_time" => "---",
                ]);

            return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
        } catch (\Exception $e) {

            return response()->json(['data' => '', 'message' => $e->getMessage(), 'status' => false]);
            // throw $th;
        }
    }

    public function quick_check_in_room(Request $request)
    {

        try {
            $id       = $request->booking_id ?? 0;
            $room_ids = $request->room_ids ?? [];

            Booking::where("id", $id)->update(['booking_status' => BookedRoom::CHECKED_IN]);

            BookedRoom::where("booking_id", $id ?? 0)
                ->whereIn("room_id", $room_ids)
                ->update([
                    'check_in'              => date('Y-m-d'),
                    'booking_status'        => BookedRoom::CHECKED_IN,
                    "room_status"           => BookedRoom::CHECKED_IN,
                    "actual_check_in_time"  => date('H:i'),
                    "actual_check_out_time" => "---",
                ]);
            return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
        } catch (\Exception $e) {

            return response()->json(['data' => '', 'message' => $e->getMessage(), 'status' => false]);
            // throw $th;
        }

        // old code below down

        try {

            // session(['isCheckInSes' => true]);

            $booking_id              = $request->booking_id;
            $booking                 = Booking::find($booking_id);
            $customer                = Customer::find($request->customer_id);
            $booking->check_in_price = $request->new_payment;
            $booking->booking_status = 2;
            $booking->id_card_no     = $request->id_card_no;
            $booking->expired        = $request->expired;
            $booking->id_card_type   = IdCardType::find($request->id_card_type_id)->name ?? "";
            $booking->check_in       = date('Y-m-d');
            $newBookingCheckIn       = date('Y-m-d');

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
                $customer->dob = date("Y-m-d");
                $customer->save();
                $this->updateTransaction($booking, $request, 'check in payment', 'credit', $request->new_payment);
                $this->updatePayment($booking, $request, $request->new_payment, 'checkin payment');
                BookedRoom::where("booking_id", $booking_id)->whereIn("room_no", $request->room_nos)->update(['check_in' => $newBookingCheckIn, 'booking_status' => 2]);
                // if (app()->isProduction()) {
                //     $customer = Customer::find($booking->customer_id);
                //     (new WhatsappNotificationController())->checkInNotification($booking, $customer);
                // }
                $customerData       = $request->only(Customer::customerAttributes());
                $customerData['id'] = $request->customer_id;
                $this->customerUpdateById($customerData);

                $this->processNotification(Template::WHEN_CUSTOMER_ARRIVED, "WHEN CUSTOMER ARRIVED", $request);

                return response()->json(['data' => '', 'message' => 'Successfully checked', 'status' => true]);
            }

            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
        } catch (\Exception $e) {

            return response()->json(['data' => '', 'message' => $e->getMessage(), 'status' => false]);
            // throw $th;
        }
    }

    public function quick_check_out_room(Request $request)
    {
        try {

            // session(['isCheckoutSes' => true]);

            $booking_id    = $request->booking_id;
            $selectedRooms = $request->selectedRooms ?? [];
            $booking       = Booking::where('company_id', $request->company_id)->find($booking_id);
            $customer      = Customer::find($booking->customer_id);
            if ($request->discount > 0) {
                $this->updateTransaction($booking, $request, 'discount', 'debit', -abs($request->discount));
                $bookedRoom = BookedRoom::whereBookingId($booking_id)->first();
                $bookedRoom->increment('room_discount', $request->discount);
            }

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $request->payment_mode_id,
                'desc'              => 'check out payment',
                'reference_number'  => $request->reference_number,
                'user_id'           => $request->user_id,
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

                $booking->save();

                BookedRoom::where("booking_id", $booking_id)
                    ->whereIn("room_id", $selectedRooms)
                    ->update(
                        [
                            "booking_status"        => BookedRoom::CHECKED_OUT,
                            "room_status"           => BookedRoom::CHECKED_OUT,
                            "is_dirty"              => 1,
                            "actual_check_out_time" => date('H:i'),
                        ]
                    );

                $this->processNotification(Template::AFTER_CHECKOUT, "AFTER CHECKOUT", $request);

                return response()
                    ->json(['bookingId' => $booking_id, 'message' => 'Successfully Paid', 'status' => true]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function check_out_room(Request $request)
    {

        DB::beginTransaction();

        try {

            // session(['isCheckoutSes' => true]);

            $company_id = $request->company_id;
            $booking_id = $request->booking_id;
            $room_id    = $request->room_id;
            $booking    = Booking::find($booking_id);
            $customer   = Customer::find($booking->customer_id);

            $whatsapp  = $customer->whatsapp;
            $email     = $customer->email;
            $title     = $customer->title;
            $full_name = $customer->full_name;

            if (! $request->isPaymentBeforeSubmitted) {

                if ($request->discount > 0) {
                    $this->updateTransaction($booking, $request, 'discount', 'debit', -abs($request->discount));
                    $bookedRoom = BookedRoom::whereBookingId($booking_id)->first();
                    $bookedRoom->increment('room_discount', $request->discount);
                }

                $transactionData = [
                    'booking_id'        => $booking->id,
                    'customer_id'       => $booking->customer_id ?? '',
                    'date'              => now(),
                    'company_id'        => $booking->company_id ?? '',
                    'payment_method_id' => $request->payment_mode_id,
                    'desc'              => 'check out payment',
                    'reference_number'  => $request->reference_number,
                    'user_id'           => $request->user_id,
                ];

                $trans = new TransactionController();

                if ($request->isHall && $request->exceedHoursCharges > 0) {
                    $transactionData["desc"] = "additional hours charges";
                    $trans->store($transactionData, $request->exceedHoursCharges ?? 0, 'debit');
                }

                $trans->store($transactionData, $request->full_payment ?? 0, 'credit');

                if ($booking->balance < 0) {

                    return response()
                        ->json(['bookingId' => $booking_id, 'message' => 'Cannot Submit Because balance is less than full payment.', 'status' => false]);
                }

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
                    $payment = Payment::whereBookingId($booking->id)
                        ->where('company_id', $booking->company_id)->where('is_city_ledger', 1)->first();
                    if ($payment) {
                        $payment->amount = (int) $booking->balance;
                        $payment->save();
                    }
                    $payment = new PaymentController();
                    $payment->store($paymentsData);
                }
            }

            $booking->booking_status = 3;
            $booking->save();

            BookedRoom::where(["booking_id" => $booking_id, "room_id" => $room_id])->update(
                [
                    "booking_status"        => BookedRoom::CHECKED_OUT,
                    "room_status"           => BookedRoom::CHECKED_OUT,
                    "is_dirty"              => 1,
                    "actual_check_out_time" => date('H:i'),
                ]
            );

            $payload = [
                "command"    => Template::AFTER_CHECKOUT,
                "heading"    => "AFTER CHECKOUT",
                "company_id" => $company_id,
                "whatsapp"   => $whatsapp,
                "email"      => $email,

                "fields"     => [
                    "title"      => $title,
                    "full_name"  => $full_name,
                    "company_id" => $company_id,
                ],
            ];

            $mediaUrl = "https://hms-backend.test/api/invoice_pdf/$booking_id";

            $mediaUrl = "https://backend.myhotel2cloud.com/api/invoice_pdf/$booking_id";
            

            if ($payload["whatsapp"]) {

                $whatsappPayload = [
                    'recipient'  => $payload["whatsapp"],
                    'text'       => (new Controller)->prepareMessage($payload['fields'], "whatsapp", $payload["command"]),
                    'company_id' => $company_id,
                    "mediaUrl"   => $mediaUrl,
                ];

                WhatsappSender::dispatch($whatsappPayload);
            }

            if ($payload["email"]) {

                $emailPayload = [
                    'recipient'  => $payload["email"],
                    'text'       => (new Controller)->prepareMessage($payload['fields'], "email", $payload["command"]),
                    'company_id' => $company_id,
                    "heading"    => $payload["heading"],
                    "mediaUrl"   => $mediaUrl,
                ];

                EmailSender::dispatch($emailPayload);

            }

            DB::commit();

            return response()
                ->json(['bookingId' => $booking_id, 'message' => 'Successfully Checked Out', 'status' => true]);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'status' => false], 500);
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
                'desc'              => $request->input('desc', 'advance payment'), // $desc 'advance payment',
                'reference_number'  => $request->reference_number,
                'user_id'           => $request->user_id,
            ];

            $payAmt = $request->new_advance;
            $meth   = 'credit';

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

            echo " Cron:  .\n" . $th;
            Logger::channel("custom")->error($th);
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
            // throw $th;
        }
    }

    public function ProcessPayment(Request $request)
    {
        // SELECT id,total_price,remaining_price,grand_remaining_price,balance,paid_amounts,advance_price,sub_total,discount,after_discount FROM bookings ORDER BY "id" desc LIMIT 1
        $payAmt   = $request->new_advance;
        $discount = (int) $request->discount;

        try {
            $booking = Booking::find($request->booking_id);

            $transactionData = [
                'booking_id'        => $booking->id,
                'customer_id'       => $booking->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $booking->company_id ?? '',
                'payment_method_id' => $request->payment_mode_id,
                'desc'              => $request->input('desc', 'payment'), // $desc 'advance payment',
                'reference_number'  => $request->reference_number,
                'user_id'           => $request->user_id,
            ];

            $paymentsData = [
                'booking_id'   => $booking->id,
                'payment_mode' => $request->payment_mode_id,
                'description'  => 'payment',
                'amount'       => $payAmt,
                'company_id'   => $booking->company_id,
                'type'         => 'room',
                'room'         => $booking->rooms,
            ];

            if ($discount > 0) {
                $booking->advance_price         = (int) $booking->advance_price + (int) $payAmt;
                $booking->paid_amounts          = (int) $booking->paid_amounts + (int) $payAmt;
                $booking->discount              = (int) $booking->discount + (int) $discount;
                $booking->after_discount        = (int) $request->after_discount;
                $booking->remaining_price       = (int) $request->after_discount - $payAmt;
                $booking->grand_remaining_price = (int) $request->after_discount - $payAmt;
                $booking->balance               = (int) $request->after_discount - $payAmt;
                $booking->save();

                $transactionDiscountData = [
                    'booking_id'        => $booking->id,
                    'customer_id'       => $booking->customer_id ?? '',
                    'date'              => now(),
                    'company_id'        => $booking->company_id ?? '',
                    'payment_method_id' => 0,
                    'desc'              => 'discount',
                    'reference_number'  => "----",
                    'user_id'           => $request->user_id,
                ];

                $this->processTransaction($booking->id, $transactionDiscountData, $discount, 'credit');
            }

            if ($payAmt > 0) {
                $booking->advance_price         = (int) $booking->advance_price + (int) $payAmt;
                $booking->paid_amounts          = (int) $booking->paid_amounts + (int) $payAmt;
                $booking->discount              = (int) $booking->discount + (int) $discount;
                $booking->after_discount        = (int) $request->after_discount;
                $booking->remaining_price       = (int) $request->after_discount - $payAmt;
                $booking->grand_remaining_price = (int) $request->after_discount - $payAmt;
                $booking->balance               = (int) $request->after_discount - $payAmt;
                $booking->save();
                // Booking::find($trans->booking_id)->update(['balance' => $trans->balance]);
                (new PaymentController())->store($paymentsData);
                $this->processTransaction($booking->id, $transactionData, $payAmt, $payAmt < 0 ? 'debit' : 'credit');
            }

            $payment = Payment::whereBookingId($booking->id)->where('is_city_ledger', 1)->first();
            if ($payment) {

                $payAmt < 0 ? $payment->amount = (int) $payment->amount + (int) $payAmt : $payment->amount = (int) $payment->amount - (int) $payAmt;
                $payment->save();
            }

            if (app()->isProduction() && $payAmt > 0) {

                $customer = Customer::find($booking->customer_id);
                (new WhatsappNotificationController())
                    ->advancePayingNotification($booking->fresh(), $customer, $payAmt, $request->payment_mode_id);
            }

            return response()->json(['data' => '', 'message' => 'Payment Successfully', 'status' => true]);
        } catch (\Throwable $th) {

            echo " Cron:  .\n" . $th;
            Logger::channel("custom")->error($th);
            return response()->json(['data' => '', 'message' => 'Unsuccessfully update', 'status' => false]);
            // throw $th;
        }
    }

    public function processTransaction($bookingId, $data, $amount, $paymentType = null)
    {
        $model   = Transaction::query();
        $payment = $model->whereBookingId($bookingId)->orderBy('id', 'desc')->first();

        if ($payment) {
            switch ($paymentType) {
                case 'credit':
                    $data['credit']  = $amount;
                    $data['balance'] = $payment->balance - $amount;
                    break;
                case 'debit':
                    $data['debit']   = $amount;
                    $data['balance'] = $payment->balance + $amount;
            }
        } else {
            $data['debit']   = $amount;
            $data['balance'] = $amount;
        }

        $model->create($data);
    }

    private function roomDetails($id)
    {
        return BookedRoom::find($id);
    }

    public function events_list(Request $request)
    {
        $date_from = date('Y-m-d', strtotime($request->startDateString . ' -7 day'));

        $date_to = $request->endDateString;

        $search = $request->search;

        return BookedRoom::whereHas('booking', function ($q) use ($request, $date_from, $date_to, ) {
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
                        $query->orWhere('contact_no', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
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
        $bookedRoom                          = BookedRoom::with(['booking', 'customer', "room"])->where('company_id', $request->company_id)->findOrFail($request->id);
        $bookedRoom->booking->booking_status = $bookedRoom->booking_status;
        $bookedRoom->booking->room_id        = $bookedRoom->room_id;
        $bookedRoom->booking->room_no        = $bookedRoom->room_no;
        $bookedRoom->booking->room_type      = $bookedRoom->room_type;
        $bookedRoom->booking->isHall         = $bookedRoom->room->room_type->type == "hall" ?? false;
        return $bookedRoom->booking;

        // return response()->json(['booking' => $bookedRoom->booking, 'status' => true]);
    }

    public function get_booked_room(Request $request)
    {
        $bookedRoom = BookedRoom::with(['booking' => function ($q) {
            $q->with(["bookedRooms" => function ($q) {
                $q->withOut("booking", "postings");
                $q->select("id", "booking_id", "room_id", "room_no", "room_type", "booking_status");
            }]);
        }, 'customer', "room", "sub_customer_room_history"])->where('company_id', $request->company_id)->findOrFail($request->id);
        $bookedRoom->booking->room_id    = $bookedRoom->room_id;
        $bookedRoom->booking->room_no    = $bookedRoom->room_no;
        $bookedRoom->booking->room_type  = $bookedRoom->room_type;
        $bookedRoom->booking->contact_no = $bookedRoom->customer->contact_no;

        $bookedRoom->posting_payment = PostingPayment::where("booking_id", $bookedRoom->booking_id)
            ->where("room_id", $bookedRoom->room_id)
            ->where("sub_customer_id", $bookedRoom->sub_customer_room_history->sub_customer_id ?? 0)->first();

        // return RoomType::HALL;

        return $bookedRoom;
        // return response()->json(['booking' => $bookedRoom->booking, 'status' => true]);
    }

    public function get_booking_for_modify(Request $request)
    {
        $payload = BookedRoom::with([
            'booking.bookedRooms',
            'customer',
            'room',
        ])->withSum('orderRooms', 'total_with_tax')
            ->withSum('orderRooms', 'base_price')
            ->withSum('orderRooms', 'grand_total')
            ->withSum('orderRooms', 'food_plan_price')
            ->withSum('orderRooms', 'bed_amount')
            ->withSum('orderRooms', 'early_check_in')
            ->withSum('orderRooms', 'late_check_out')
            ->withSum('orderRooms', 'late_check_out')
            ->withSum('orderRooms', 'late_check_out')
            ->where('id', $request->id)
            ->first();

        // $payload = BookedRoom::with([
        //     'booking.bookedRooms',
        //     'customer',
        //     'room',
        // ])
        //     ->withSum(['orderRooms as total_with_tax_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'total_with_tax')
        //     ->withSum(['orderRooms as base_price_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'base_price')
        //     ->withSum(['orderRooms as grand_total_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'grand_total')
        //     ->withSum(['orderRooms as food_plan_price_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'food_plan_price')
        //     ->withSum(['orderRooms as bed_amount_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'bed_amount')
        //     ->withSum(['orderRooms as early_check_in_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'early_check_in')
        //     ->withSum(['orderRooms as late_check_out_sum' => function ($query) use ($request) {
        //         $query->where('booked_room_id', $request->id);
        //     }], 'late_check_out')
        //     ->where('id', $request->id)
        //     ->first();

        if ($payload) {
            $payload->booked_room_count = BookedRoom::where("booking_id", $request->booking_id)->count() ?? 0;
        }

        return $payload;
    }

    public function changeCheckIntoBookingAdmin(Request $request, $id)
    {
        try {
            $company_id            = $request->company_id;
            $cancel_checkin_userid = $request->cancel_checkin_userid;
            $cancel_checkin_reason = $request->cancel_checkin_reason;
            $booking_id            = $request->booking_id;
            $booked_room_id        = $request->booked_room_id;
            //change booking status
            $bookingModel = Booking::where('company_id', $company_id)
                ->where('id', $booking_id)
                ->where('booking_status', 2) //only checkedin status
            ;

            $data1 = [
                'booking_status'          => 1,
                'cancel_checkin_reason'   => $cancel_checkin_reason,
                'cancel_checkin_datetime' => date('Y-m-d H:i:s'),
                'cancel_checkin_userid'   => $cancel_checkin_userid,
            ];
            $updatedStatus = $bookingModel->update($data1);

            if ($updatedStatus) {
                //change status on booking_rooms table
                $bookingRoomModel = BookedRoom::where('company_id', $company_id)
                    ->where('id', $booked_room_id)
                    ->where('booking_status', 2) //only checkedin status
                ;
                $data2 = [
                    'booking_status'          => 1,
                    'cancel_checkin_reason'   => $cancel_checkin_reason,
                    'cancel_checkin_datetime' => date('Y-m-d H:i:s'),
                    'cancel_checkin_userid'   => $cancel_checkin_userid,
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
            $model         = BookedRoom::find($id);
            $numberOfRooms = BookedRoom::where('booking_id', $model->booking_id)->count();
            $bookingId     = $model->booking_id;

            $bookedRoom = $model;
            if ($bookedRoom) {
                $bookedRoom->reason    = $request->reason;
                $bookedRoom->cancel_by = $request->cancel_by;
                $bookedRoom->action    = $request->action ?? "Cancel by manual";

                $bookedRoom->status_before_cancelation = $model->booking_status;

                $status_before_cancelation_msg             = $model->booking_status == 1 ? "Cancelled Before Check-in" : "Cancelled After Check-in";
                $bookedRoom->status_before_cancelation_msg = $status_before_cancelation_msg;

                $arr    = $bookedRoom->toArray();
                $cancel = CancelRoom::create($arr);
                if ($cancel) {
                    OrderRoom::whereBookedRoomId($id)->delete();

                    OrderRoom::where("booking_id", $model->booking_id)->delete();

                    $transactionData = [
                        'booking_id'        => $bookedRoom->booking_id,
                        'customer_id'       => $bookedRoom->customer_id ?? '',
                        'date'              => now(),
                        'company_id'        => $bookedRoom->company_id ?? '',
                        'payment_method_id' => 7,
                        'desc'              => "room $model->room_no canceled",
                        'user_id'           => $request->cancel_by,
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
        $string      = implode(', ', $bookedRooms);
        return $string;
    }

    public function getTaxSlab($amount, $company_id)
    {
        $amount = (int) $amount;
        $tax    = env('GST_TAX_DEFAULT');

        $TaxSlab = TaxSlabs::where('company_id', $company_id)
            ->where('start_price', '<=', $amount)
            ->where('end_price', '>=', $amount)
            ->pluck('tax');

        if (isset($TaxSlab[0])) {
            $tax = $TaxSlab[0];
        }

        return $tax;
    }

    private function getRoomTax($amount, $company_id)
    {
        $temp = [];
        // $per = $amount < 2500 ? 12 : 18;
        // if ($amount > 7500) {
        //     $per = 28;
        // }

        $per = $this->getTaxSlab($amount, $company_id);

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
        $data                = $this->getRoomTax($afterDiscount, $request->company_id);
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

    public function modifyBooking(Request $request)
    {
        $room_orders = $request->room_orders;

        $check_in  = $request->json["check_in"] . " 12:00";
        $check_out = $request->json["check_out"] . " 11:00";
        $room_id   = $request->json["room_id"];
        $room_no   = $request->json["room_no"];
        $room_type = $request->json["room_type"];

        $no_of_adult = $request->json["no_of_adult"];
        $no_of_child = $request->json["no_of_child"];

        $breakfast = $request->json["breakfast"] ?? 0;
        $lunch     = $request->json["lunch"] ?? 0;
        $dinner    = $request->json["dinner"] ?? 0;

        $food_plan_id    = $request->json["food_plan_id"];
        $food_plan_price = $request->json["food_plan_price"] ?? 0;

        $extra_bed_qty = $request->json["extra_bed_qty"] ?? 0;
        $bed_amount    = $request->json["bed_amount"] ?? 0;

        $early_check_in = $request->json["early_check_in"] ?? 0;
        $late_check_out = $request->json["late_check_out"] ?? 0;

        $user_id    = $request->json["user_id"];
        $company_id = $request->json["company_id"];

        $booking_total_price = 0;

        $company_food_tax = Company::whereId($company_id)->pluck('food_tax')->first();

        OrderRoom::where('booking_id', $request->old["booking_id"])->where("booked_room_id", $request->json["booked_room_id"])->delete();

        $bookingDiscount = $request->json["discount"];    //  $request->old["booking"]["discount"];
        $bookingExtra    = $request->json["total_extra"]; //$request->old["booking"]["total_extra"];

        $singleDayDiscount    = ($bookingDiscount / count($room_orders));
        $singleDayExtraAmount = ($bookingExtra / count($room_orders));

        $food_price_per_room       = $food_plan_price; //($food_plan_price * ($no_of_adult + ($no_of_child / 2)));
        $singleDayAdditionalAmount = $food_price_per_room + $singleDayExtraAmount + (($bed_amount + $early_check_in + $late_check_out) / count($room_orders));

        $arr = [];

        foreach ($room_orders as $room_order) {

            $orderRooms = [];

            $orderRooms['room_change_notes'] = $request->notes;

            $orderRooms['room_discount']  = $singleDayDiscount;
            $orderRooms['price']          = $room_order['room_price']; //without tax
            $orderRooms['total_with_tax'] = $room_order['price'];      //with tax

            $total = $room_order['price'] + $singleDayAdditionalAmount - $singleDayDiscount;

            $orderRooms['total']       = $room_order['price'] + $singleDayAdditionalAmount - $singleDayExtraAmount;
            $orderRooms['grand_total'] = $total;

            $orderRooms['booked_room_id'] = $request->json["booked_room_id"];
            $orderRooms['company_id']     = $company_id;
            $orderRooms['customer_id']    = $request->old["customer"]["id"];
            $orderRooms['no_of_baby']     = $request->old["no_of_baby"];

            $orderRooms['booking_id']      = $request->old["booking_id"];
            $orderRooms['date']            = $room_order['date'];
            $orderRooms['room_id']         = $room_id;
            $orderRooms['room_no']         = $room_no;
            $orderRooms['room_type']       = $room_type;
            $orderRooms['check_in']        = $check_in;
            $orderRooms['check_out']       = $check_out;
            $orderRooms['days']            = count($room_orders ?? 0) ?? 0;
            $orderRooms['food_plan_id']    = $food_plan_id;
            $orderRooms['breakfast']       = $breakfast;
            $orderRooms['lunch']           = $lunch;
            $orderRooms['dinner']          = $dinner;
            $orderRooms['no_of_adult']     = $no_of_adult;
            $orderRooms['no_of_child']     = $no_of_child;
            $orderRooms['extra_bed_qty']   = $extra_bed_qty;
            $orderRooms['meal']            = $request->json["meal_name"];
            $orderRooms['food_plan_price'] = $food_plan_price;
            $orderRooms['bed_amount']      = round($bed_amount / count($room_orders), 2);
            $orderRooms['early_check_in']  = round($early_check_in / count($room_orders), 2);
            $orderRooms['late_check_out']  = round($late_check_out / count($room_orders), 2);

            $orderRooms['tariff'] = $room_order["day_type"];

            $orderRooms['day'] = $room_order['day'] ?? null;

            $orderRooms['created_at'] = date("Y-m-d H:i:s");
            $orderRooms['updated_at'] = date("Y-m-d H:i:s");

                                                                            //calculate inv room price-------START-----------------------------------------------
            $orderRooms['single_day_extra_amount'] = $singleDayExtraAmount; //new
            $orderRooms['single_day_discount']     = $singleDayDiscount;    //new

            $result                 = $this->divideTaxPrice($total, $total, $company_id);
            $room_price_without_tax = $result[0];
            $room_tax               = $result[1];
            $orderRooms['price']    = $room_price_without_tax;
            $orderRooms['cgst']     = $room_tax / 2;
            $orderRooms['sgst']     = $room_tax / 2;
            $orderRooms['room_tax'] = $room_tax;

            //recalculate price and miscellaneous and tax------------------------------------------------
            $miscellaneous_total_with_tax = $orderRooms['bed_amount']
                 + $orderRooms['food_plan_price']
                 + $orderRooms['early_check_in']
                 + $orderRooms['late_check_out']
                 + $orderRooms['single_day_extra_amount'];

            $miscellaneous_without_extra_discount = $miscellaneous_total_with_tax - $orderRooms['single_day_discount'];

            $orderRooms['base_price'] = $room_price_without_tax - $miscellaneous_without_extra_discount;

            $room_price_with_tax                  = $room_order['price'] - $orderRooms['single_day_discount'];
            $result                               = $this->divideTaxPrice($room_price_with_tax, $room_price_with_tax, $company_id);
            $room_price_without_tax               = $result[0];
            $room_tax                             = $result[1];
            $room_tax_percentage                  = $result[2];
            $orderRooms['inv_room_listing_price'] = $room_price_without_tax;
            $orderRooms['inv_room_cgst']          = round($room_tax / 2, 2);
            $orderRooms['inv_room_sgst']          = round($room_tax / 2, 2);
            $orderRooms['inv_room_tax_per']       = $room_tax_percentage;

            $miscellaneous_total_without_tax               = ($miscellaneous_total_with_tax * 100) / (100 + $company_food_tax);
            $miscellaneous_tax                             = $miscellaneous_total_with_tax - $miscellaneous_total_without_tax;
            $orderRooms['miscellaneous_total']             = $miscellaneous_total_with_tax;    //new
            $orderRooms['miscellaneous_total_without_tax'] = $miscellaneous_total_without_tax; //new
            $orderRooms['miscellaneous_tax']               = $miscellaneous_tax;               //new

            $orderRooms['inv_food_tax_per'] = $company_food_tax;
            //-----------------------END

            $arr[] = $orderRooms;
        }

        $booking_total_price = $request->json["new_total"];
        OrderRoom::insert($arr);

        unset($arr[0]["tariff"]);
        unset($arr[0]["day"]);
        unset($arr[0]["base_price"]);
        unset($arr[0]["booked_room_id"]);
        unset($arr[0]["date"]);
        unset($arr[0]["price_adjusted_after_dsicount"]);

        unset($arr[0]["inv_room_listing_price"]);
        unset($arr[0]["inv_room_cgst"]);
        unset($arr[0]["inv_room_sgst"]);
        unset($arr[0]["miscellaneous_total"]);
        unset($arr[0]["miscellaneous_total_without_tax"]);
        unset($arr[0]["miscellaneous_tax"]);
        unset($arr[0]["single_day_extra_amount"]);
        unset($arr[0]["single_day_discount"]);

        unset($arr[0]["inv_room_tax_per"]);
        unset($arr[0]["inv_food_tax_per"]);
        unset($arr[0]["room_change_notes"]);

        BookedRoom::where('id', $request->json["booked_room_id"])->update($arr[0]);
        $credit = Transaction::where("booking_id", $request->old["booking_id"])->sum("credit");

        $debit = $booking_total_price - $request->old['booking_total_price'];

        $totalPostingAmount = Posting::whereBookingId($request->old["booking_id"])->sum('amount_with_tax') ?? 0;

        $balance = ($totalPostingAmount + $booking_total_price) - $credit;

        $arr = [
            "desc"        => "room change new price ($booking_total_price)",
            "balance"     => $balance,
            "debit"       => $debit,
            "booking_id"  => $request->old["booking_id"],
            "user_id"     => $user_id,
            "customer_id" => $request->old["customer_id"],
            "company_id"  => $company_id,
            'date'        => now(),
        ];

        Transaction::create($arr);

        $remaining_price       = $balance;
        $grand_remaining_price = $balance;

        $bookingPayload = [
            'total_days'            => $request->json["total_days"],
            'user_id'               => $request->json["user_id"],
            'sub_total'             => $request->json["total_price"], // use for sub_total like 12320
            'total_price'           => $request->json["total"],       // use for sub_total like 12000
            'balance'               => $balance,
            'remaining_price'       => $remaining_price,
            'grand_remaining_price' => $grand_remaining_price,
            'discount'              => $request->json["discount"],
            'check_in'              => $check_in,
            'check_out'             => $check_out,
        ];

        Booking::where("id", $request->old["booking_id"])
            ->where("company_id", $company_id)
            ->update($bookingPayload);

        return $this->response('Booking has been modified.', null, true);
    }

    public function divideTaxPrice($slabtotal, $total, $company_id)
    {
        $BookingObj    = new BookingController();
        $room_tax      = $BookingObj->getTaxSlab(($slabtotal), $company_id);
        $roomBasePrice = ($total * 100) / (100 + $room_tax);
        $roomGSTAmount = $total - $roomBasePrice;
        // $orderRooms['price'] = $roomBasePrice;
        // $orderRooms['cgst'] = $roomGSTAmount / 2;
        // $orderRooms['sgst'] = $roomGSTAmount / 2;
        // $orderRooms['room_tax'] = $roomGSTAmount;

        $room_tax_new = $BookingObj->getTaxSlab(($roomBasePrice), $company_id);

        if ($room_tax_new != $room_tax) {
            $room_tax      = $BookingObj->getTaxSlab(($roomBasePrice), $company_id);
            $roomBasePrice = ($total * 100) / (100 + $room_tax);
            $roomGSTAmount = $total - $roomBasePrice;
            // $orderRooms['price'] = $roomBasePrice;
            // $orderRooms['cgst'] = $roomGSTAmount / 2;
            // $orderRooms['sgst'] = $roomGSTAmount / 2;
            // $orderRooms['room_tax'] = $roomGSTAmount;
        }

        return [$roomBasePrice, $roomGSTAmount, $room_tax];
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
            ->filter(request('search'));

        if ($request->filled('is_cash')) {
            $model->whereDoesntHave('customer', function ($q) {
                $q->whereNotNull('gst_number')
                    ->orWhereHas('source', function ($q2) {
                        $q2->whereNotNull('gst');
                    });
            });
        }

        $model->where('room_category_type', null);

        $model->whereHas('bookedRooms', function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
        });

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', env("WILD_CARD") ?? 'ILIKE', '%' . $request->source . '%');
        }

        if ($request->isSelectAll != -1) {
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
        }

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
            default: ;
        }

        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type,booking_status',
                'customer:id,first_name,last_name,document,source_id,gst_number',
            ])
            ->where('company_id', $request->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);
    }

    public function bookingInvoices(Request $request)
    {
        $model = Booking::query()
            ->latest()
            ->filter(request('search'));

        $model->whereHas('bookedRooms', function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
        });

        if ($request->filled('status') && $request->status == "Unpaid") {
            $model->where('balance', ">", 0);
        } else if ($request->filled('status') && $request->status == "Paid") {
            $model->where('balance', 0);
        }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereBetween('booking_date', [$request->from, $request->to]);
        }

        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type,booking_status',
                'customer:id,first_name,last_name,document,contact_no',
                'postings.room',
            ])
            ->with(['orderRooms' => function ($q) {
                $q->withOut(['booking', "postings"]);
            }])
            ->where('company_id', $request->company_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 20);
    }

    public function allReservationList(Request $request)
    {
        return $this->getReservationList($request, '');
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
        $newRoomDetails             = $this->getDataBySelectWithTax($oldRoom, $newRoom, $request);
        $newRoomEachDay             = $newRoomDetails['data'];
        $totalNewRoomTax            = array_sum(array_column($newRoomEachDay, 'tax'));
        $newRoomAmount              = array_sum(array_column($newRoomEachDay, 'price'));
        $afterDiscountNewRoomAmount = $newRoomAmount - (float) $oldRoom->room_discount;
        $newRoomGrandAmount         = $afterDiscountNewRoomAmount + $oldRoom->tot_adult_food + $oldRoom->tot_child_food;
        $numberOfDay                = count($newRoomEachDay);
        $diff                       = $newRoomGrandAmount - (float) $oldRoom->grand_total;

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
        $oldRoomRoomNo           = $oldRoom->room_no;
        $oldRoomCategory         = $oldRoom->room_type;
        $newRoomNo               = $newRoomDetails['room']['room_no'] ?? "";
        $newRoomCategory         = $newRoomDetails['room']['room_type']['name'] ?? "";
        $oldRoom->room_id        = $newRoomDetails['room']['id'] ?? "";
        $oldRoom->room_no        = $newRoomNo ?? "";
        $oldRoom->room_type      = $newRoomCategory ?? "";
        $oldRoom->price          = $newRoomAmount;
        $oldRoom->after_discount = $afterDiscountNewRoomAmount;
        $oldRoom->check_in       = $request->start;
        $oldRoom->check_out      = $request->end;
        $oldRoom->cgst           = $totalNewRoomTax / 2;
        $oldRoom->sgst           = $totalNewRoomTax / 2;
        $oldRoom->room_tax       = $totalNewRoomTax;
        $oldRoom->grand_total    = $newRoomGrandAmount;
        $oldRoom->total          = $newRoomGrandAmount;
        $oldRoom->save();

        // return $oldRoom;

        $arr = [
            'payment_mode_id' => 7,
            'user_id'         => $request->user_id,
        ];
        $msg     = "$oldRoomCategory room no $oldRoomRoomNo change to $newRoomCategory $newRoomNo";
        $booking = Booking::whereId($oldRoom->booking_id)->first();
        $this->updateTransactionByArr($booking, $arr, "$msg", 'debit', $diff);
        $this->updatePaymentByArr($booking, $arr, $diff, $msg);

        $orderRoomObj    = OrderRoom::whereBookedRoomId($oldRoom->id)->first();
        $orderRoomDelete = OrderRoom::whereBookedRoomId($oldRoom->id)->delete();

        if ($orderRoomDelete) {
            foreach ($newRoomEachDay as $singleDay) {
                $orderRoomObj->date                = $singleDay['date'];
                $orderRoomObj->room_no             = $newRoomDetails['room']['room_no'];
                $orderRoomObj->room_type           = $newRoomDetails['room']['room_type']['name'];
                $orderRoomObj->price               = $singleDay['price'];
                $orderRoomObj->room_tax            = $singleDay['tax'];
                $orderRoomObj->sgst                = $singleDay['tax'] / 2;
                $orderRoomObj->cgst                = $singleDay['tax'] / 2;
                $orderRoomObj->grand_total         = $newRoomGrandAmount / $numberOfDay;
                $orderRoomObj->total_with_discount = $afterDiscountNewRoomAmount / $numberOfDay;
                $orderRoomObj->after_discount      = $afterDiscountNewRoomAmount / $numberOfDay;
                $orderRoomObj->total               = $newRoomGrandAmount / $numberOfDay;
                $orderRoomObj->total_with_tax      = $afterDiscountNewRoomAmount / $numberOfDay;
                OrderRoom::create($orderRoomObj->toArray());
            }
        }

        return true;

        return [
            'newRoomDetails' => $newRoomDetails,
            'oldRoom'        => $oldRoom,
        ];
    }

    private function updateTransactionByArr($booking, $arr, $desc = "", $mode, $amt)
    {
        $transactionData = [
            'booking_id'        => $booking->id,
            'customer_id'       => $booking->customer_id ?? '',
            'date'              => now(),
            'company_id'        => $booking->company_id ?? '',
            'payment_method_id' => $arr['payment_mode_id'],
            'desc'              => $desc,
            'reference_number'  => $arr['reference_number'] ?? "",
            'user_id'           => $arr['user_id'],
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
        $discount   = $request->discount ?? 0;
        $room       = Room::where('room_no', $newRoom->room_no)->where('company_id', $request->company_id)->first();
        $prices     = RoomType::whereCompanyId($request->company_id)->whereName($newRoom->roomType->name)
            ->first(['holiday_price', 'weekend_price', 'weekday_price']);

        $weekModel = Weekend::where('company_id', $request->company_id)->first();
        $weekends  = $weekModel->day;

        $arr    = [];
        $period = CarbonPeriod::create($request->start, $this->checkOutDate($request->end));
        foreach ($period as $date) {
            $iteration_date = $date->format('Y-m-d');
            $day            = $date->format('D');
            $isWeekend      = in_array($day, $weekends);
            $isHoliday      = $this->checkHoliday($iteration_date, $company_id);
            if ($isHoliday) {
                $arr[] = [
                    "date"       => $iteration_date,
                    "price"      => $this->getRoomTax($prices->holiday_price - $discount, $request->company_id)['total_with_tax'],
                    "day_type"   => "holiday",
                    "day"        => $day,
                    "tax"        => $this->getRoomTax($prices->holiday_price - $discount, $request->company_id)['room_tax'],
                    "room_price" => $prices->holiday_price,
                ];
            } elseif ($isWeekend) {
                $arr[] = [
                    "date"       => $iteration_date,
                    "price"      => $this->getRoomTax($prices->weekend_price - $discount, $request->company_id)['total_with_tax'],
                    "tax"        => $this->getRoomTax($prices->weekend_price - $discount, $request->company_id)['room_tax'],
                    "day_type"   => "weekend",
                    "day"        => $day,
                    "room_price" => $prices->weekend_price,
                ];
            } else {
                $arr[] = [
                    "date"       => $iteration_date,
                    "price"      => $this->getRoomTax($prices->weekday_price - $discount, $request->company_id)['total_with_tax'],
                    "day_type"   => "weekday",
                    "day"        => $day,
                    "tax"        => $this->getRoomTax($prices->weekday_price - $discount, $request->company_id)['room_tax'],
                    "room_price" => $prices->weekday_price,
                ];
            }
        }

        return [
            'room'        => $room,
            'data'        => $arr,
            'total_price' => array_sum(array_column($arr, "price")),
            'total_tax'   => array_sum(array_column($arr, "tax")),
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

        DB::beginTransaction();
        try {
            $request['customer_id'] = $this->customerStore($request->only(Customer::customerAttributes()));
            //$booking = $this->storeBooking($request);

            $bookingArray               = $this->storeGroupBooking($request);
            $booking_reservation_number = $bookingArray[1];
            $booking                    = $bookingArray[0];

            if ($booking) {

                $data = [
                    'selectedRooms'     => $request->input('selectedRooms'),
                    'room_discount'     => $request->input('room_discount'),
                    'room_extra_amount' => $request->input('room_extra_amount'),
                    'booking_id'        => $booking->id,
                    'customer_id'       => $request['customer_id'],
                    'company_id'        => $request->company_id ?? 3,
                    'booking_status'    => $booking->booking_status,
                ];
                StoreBookedRoomsJob::dispatch($data);

                //recalculating Tax based on discount
                // (new ManagementController())->generateOccupancyRateByBooking($request);

                if ($request->filled("payment_reference_id")) {
                    $data                         = [];
                    $data['payment_reference_id'] = $request->payment_reference_id;
                    $data['payment_response']     = json_encode($request->payment_response);

                    Booking::whereId($booking->id)->update($data);
                }
            }

            DB::commit();

            $this->processNotification(Template::BOOKING_CREATE, "BOOKING CONFIRMED", $request, $booking_reservation_number);

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

        $data                          = [];
        $data                          = $request->only(Booking::bookingAttributes());
        $data['booking_date']          = date("Y-m-d");
        $data['payment_status']        = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
        $data['remaining_price']       = (float) $request->total_price - (float) $request->advance_price;
        $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
        $data['reservation_no']        = $this->getReservationNumber($data);
        $data['verified']              = Booking::VERIFICATION_REQUIRED;
        $data['booking_type']          = $request->booking_type ?? "room";

        $data['discount']    = $request->room_discount ?? 0;
        $data['total_extra'] = $request->room_extra_amount ?? 0;

        if ($request->filled('api_json_reference_number')) {
            $data['widget_confirmation_number'] = $request->api_json_reference_number;
        }

        $booked = Booking::create($data);

        if ($booked) {

            $transactionData = [
                'booking_id'        => $booked->id,
                'customer_id'       => $booked->customer_id ?? '',
                'date'              => now(),
                'company_id'        => $request->company_id ?? '',
                'desc'              => 'rooms booking amount',
                'reference_number'  => $request->reference_number,
                'payment_method_id' => 7,
                'user_id'           => $request->user_id,
            ];

            //Transaction
            $payment = new TransactionController();
            $payment->store($transactionData, $request->total_price, 'debit');

            if ($request->advance_price && $request->advance_price > 0) {
                $transactionData['desc']              = 'payment';
                $transactionData['payment_method_id'] = $booked->payment_mode_id;

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
                        'description'    => $booked->source,
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
                        'description'    => $booked->source,
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
            ->where('booking_status', '!=', 0)
            ->where('room_id', $request->selectedRooms[0]['room_id'])
            ->count();

        if ($bookedRoomsCount > 0) {
            return response()->json(['error' => 'Room is not availalbe on this Date']); // return a user-friendly error
        }

        DB::beginTransaction();
        try {
            $customer_id            = $this->customerStore($request->only(Customer::customerAttributes()));
            $request['customer_id'] = $customer_id;
            //$booking = $this->storeBooking($request);

            $bookingArray               = $this->storeGroupBooking($request);
            $booking_reservation_number = $bookingArray[1];
            $booking                    = $bookingArray[0];

            if ($booking) {
                $this->storeBookedRoomsForHall($request, $booking);
                //recalculating Tax based on discount
                (new ManagementController())->generateOccupancyRateByBooking($request);

                if ($request->filled("payment_reference_id")) {
                    $data                         = [];
                    $data['payment_reference_id'] = $request->payment_reference_id;
                    $data['payment_response']     = json_encode($request->payment_response);

                    Booking::whereId($booking->id)->update($data);
                }
            }

            DB::commit();

            $this->processNotification(Template::BOOKING_CREATE, "BOOKING CREATE", $request);

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

                $room['booking_id']     = $booking->id;
                $room['customer_id']    = $booking->customer_id;
                $room['booking_status'] = $booking->booking_status;

                $room['check_in']  = $booking->check_in;
                $room['check_out'] = $booking->check_out;

                $priceList = $room['priceList'];

                unset($room['priceList']);
                unset($room['meal_name']);
                unset($room['extra_hours_charges']);
                unset($room['check_in_time']);
                unset($room['check_out_time']);

                $bookedRoomId = BookedRoom::create($room);

                $orderRooms           = array_intersect_key($room, array_flip(OrderRoom::orderRoomAttributes()));
                $singleDayDiscount    = ($room['room_discount'] / count($priceList));
                $singleDayExtraAmount = ($room['room_extra_amount'] / count($priceList));
                // $singleDayPrice = ($room['price'] / count($priceList));

                foreach ($priceList as $list) {
                    $singleDayPrice = $list['room_price'];
                    // Recalculation start
                    $taxArray = $this->reCalculatePrice($list['price'] - $singleDayDiscount + $singleDayExtraAmount);

                    $price_adjusted_after_dsicount = $taxArray['basePrice'];
                    $list['tax']                   = $taxArray['gstAmount'];
                    // Recalculation end

                    $orderRooms['price_adjusted_after_dsicount'] = $price_adjusted_after_dsicount;
                    $orderRooms['date']                          = $list['date'];

                    $orderRooms['room_discount']  = $singleDayDiscount;
                    $orderRooms['after_discount'] = $list['price'] - $orderRooms['room_discount'] + $singleDayExtraAmount;

                    $orderRooms['price'] = $list['price'];

                    $orderRooms['total_with_tax'] = $orderRooms['after_discount'];

                    $orderRooms['total']       = $orderRooms['total_with_tax'];
                    $orderRooms['grand_total'] = $orderRooms['total_with_tax'];

                    $orderRooms['days']            = 1;
                    $orderRooms['room_tax']        = $list['tax'];
                    $orderRooms['sgst']            = $list['tax'] / 2;
                    $orderRooms['cgst']            = $list['tax'] / 2;
                    $orderRooms['booked_room_id']  = $bookedRoomId->id;
                    $orderRooms['customer_id']     = $bookedRoomId->customer_id;
                    $orderRooms['meal']            = $bookedRoomId->meal;
                    $orderRooms['no_of_adult']     = $bookedRoomId->no_of_adult;
                    $orderRooms['no_of_child']     = $bookedRoomId->no_of_child;
                    $orderRooms['no_of_baby']      = $bookedRoomId->no_of_baby;
                    $orderRooms['food_plan_id']    = $bookedRoomId->food_plan_id;
                    $orderRooms['food_plan_price'] = $bookedRoomId->food_plan_price;
                    $orderRooms['early_check_in']  = $bookedRoomId->early_check_in;
                    $orderRooms['late_check_out']  = $bookedRoomId->late_check_out;

                    $orderRooms['cleaning']    = $bookedRoomId->cleaning;
                    $orderRooms['electricity'] = $bookedRoomId->electricity;
                    $orderRooms['generator']   = $bookedRoomId->generator;
                    $orderRooms['audio']       = $bookedRoomId->audio;
                    $orderRooms['projector']   = $bookedRoomId->projector;

                    $orderRooms['hall_min_hours']              = $bookedRoomId->hall_min_hours;
                    $orderRooms['extra_hours']                 = $bookedRoomId->extra_hours;
                    $orderRooms['total_booking_hours']         = $bookedRoomId->total_booking_hours;
                    $orderRooms['extra_booking_hours_charges'] = $bookedRoomId->extra_booking_hours_charges;

                    $orderRooms['extra_bed_qty '] = 0;

                    OrderRoom::create($orderRooms);
                }
            }

            // if (app()->isProduction()) {
            //     $customer = Customer::find($booking->customer_id);
            //     (new WhatsappNotificationController())->whatsappNotification($booking, $rooms['selectedRooms'], $customer, 'booking');
            // }

            return $rooms;
            return $this->response('Room Booked Successfully.', $rooms, true);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getRoomStatusColorCode($status)
    {
        $colors = (new BookingController())->RoomColorCodes();
        $index  = array_search($status, array_column($colors, 'status_id'));
        return $index !== false ? $colors[$index]['color'] : null;
    }
    public function RoomColorCodes()
    {
        return [
            ['status_id' => 0, 'color' => '#4caf50', "desc" => "avaialbe"],
            ['status_id' => 1, 'color' => '#538234', "desc" => "booked"],
            ['status_id' => 2, 'color' => '#0f642b', "desc" => "checkedin"],
            ['status_id' => 3, 'color' => '#fb0103', "desc" => "checkedout/dirty"],
            ['status_id' => 4, 'color' => '#92d051', "desc" => "expected_arrival"],
            ['status_id' => 5, 'color' => '#92d051', "desc" => "avaialbe"],
            ['status_id' => 6, 'color' => '#71de36', "desc" => "booking done-nopayment"],
            ['status_id' => 7, 'color' => '#c55a12', "desc" => "expected checkout"],
            ['status_id' => 8, 'color' => '#ffc000', "desc" => "checkedout but due payment"],

            ['status_id' => 11, 'color' => '#04b0f2', "desc" => "OTA Online Travel Agency"],
            ['status_id' => 12, 'color' => '#7551e5', "desc" => "Walkin customer "],
            ['status_id' => 13, 'color' => '#ff00dc', "desc" => "Travel agent"],
            ['status_id' => 14, 'color' => '#010002', "desc" => "Compliment"],
            ['status_id' => 15, 'color' => '#c65a12', "desc" => "Corporate"],

        ];
    }

    public function getOrderRoomData($id)
    {
        return OrderRoom::with("foodplan")->where("booking_id", $id)->first();
    }

    public function getTenDaysForecast($id = 0)
    {
        $today = Carbon::tomorrow();

        $AvailableRooms = Room::with("is_cleaned")
            ->where('company_id', $id)
            ->whereNot("status", Room::Blocked)
            ->count();

        $dates = [];

        for ($i = 0; $i < 10; $i++) {

            $date = date("Y-m-d", strtotime("+$i days", strtotime($today)));

            $bookedData = Room::whereHas('bookedRoom', function ($q) use ($id, $date) {
                $q->whereNotNull('room_id');
                $q->where('company_id', $id);
                $q->where(function ($query) use ($date) {
                    // Check if the check-in is before or equal to today, and check-out is after or equal to today
                    $query->whereDate('check_in', '<=', $date)
                        ->whereDate('check_out', '>=', $date)
                        ->where('booking_status', BookedRoom::BOOKED) // Status for dirty rooms
                        ->where('booking_status', '!=', 0);           // Exclude non-active bookings
                });
            })->count();

            $dates[$date] = [
                "label"            => date("D", strtotime($date)),
                "bookedCount"      => $bookedData,
                "bookedPercent"    => round(($bookedData / $AvailableRooms) * 100, 2),
                "availableCount"   => $AvailableRooms - $bookedData,
                "availablePercent" => round((($AvailableRooms - $bookedData) / $AvailableRooms) * 100, 2),
            ];
        }

        return array_values($dates);
    }
    public function bookingStatsBySourceType(Request $request)
    {
        $company_id = $request->company_id;

        $CustomerCount = Customer::whereCompanyId($company_id)->count();

        $sourceCounts = Source::whereCompanyId($company_id)->whereHas('bookings')->get()->groupBy('type');

        $sourceCountArray = [];
        foreach ($sourceCounts as $key => $sourceCount) {
            $sourceCountArray[$key] = count($sourceCount->toArray());
        }

        return [
            [
                'icon'  => 'mdi-laptop',
                'value' => isset($sourceCountArray['Online'])
                ? str_pad($sourceCountArray['Online'], 2, '0', STR_PAD_LEFT)
                : '00',
                'label' => 'OTA',
                'col'   => 7,
                'color' => 'blue', // For online/technology (OTA)
            ],
            [
                'icon'  => 'mdi-account-tie',
                'value' => isset($sourceCountArray['Corporate'])
                ? str_pad($sourceCountArray['Corporate'], 2, '0', STR_PAD_LEFT)
                : '00',
                'label' => 'Corporate',
                'col'   => 7,
                'color' => 'orange', // For business (Corporate)
            ],
            [
                'icon'  => 'mdi-account-tie-outline',
                'value' => isset($sourceCountArray['Travel Agency'])
                ? str_pad($sourceCountArray['Travel Agency'], 2, '0', STR_PAD_LEFT)
                : '00',
                'label' => 'Travel Agent',
                'col'   => 7,
                'color' => 'teal', // For service/people (Travel Agent)
            ],
            [
                'icon'  => 'mdi-account-outline',
                'value' => str_pad($CustomerCount, 2, '0', STR_PAD_LEFT),
                'label' => 'Customers',
                'col'   => 7,
                'color' => 'purple', // For activity (Walking)
            ],
        ];
    }

    public function processNotification($action, $heading, $request, $reservation = null)
    {

        $check_in  = date('d-M-y H:i', strtotime($request->check_in));
        $check_out = date('d-M-y H:i', strtotime($request->check_out));

        $title = ucfirst($request->title) ?? 'Mr';

        $full_name = ucfirst($request->first_name) . " " . ucfirst($request->last_name) ?? 'Guest';

        $email    = $request->email ?? "---";
        $whatsapp = $request->whatsapp ?? "---";

        // $payment_mode = PaymentMode::whereId($request->payment_mode_id)->value("name") ?? "---";

        $total_price = number_format($request->total_price) ?? "---";
        $no_of_adult = array_sum(array_column($request->selectedRooms ?? [], "no_of_adult")) ?? 1;

        $room_type = $request->room_type ?? "---";
        $room_no   = $request->room_no ?? "---";

        $nights = $request->total_days ?? 1;

        $company_id = $request->company_id;

        $mediaUrl = null;

        if ($action == Template::BOOKING_CREATE) {

            $pdfPayload = [
                "reservation_no" => $reservation,
                "booked_date"    => date("d M Y"),
                'check_in'       => $check_in,
                'check_out'      => $check_out,
                'guests'         => "$no_of_adult Guests",
                'primary_guest'  => "$title $full_name",
                'email'          => $email,
                'phone'          => $whatsapp,
                'room_type'      => $room_type,
                // 'room_no'        => $room_no,
                'adults'         => $no_of_adult,
                'total_price'    => $total_price,
                "nights"         => $nights,
            ];
            $mediaUrl = (new Booking)->voucher($pdfPayload);
        }

        $payload = [
            "command"    => $action,
            "heading"    => $heading,
            "company_id" => $company_id,
            "whatsapp"   => $whatsapp,
            "email"      => $email,

            "fields"     => [
                "title"          => $title,
                "full_name"      => $full_name,
                "from_date"      => $check_in,
                "to_date"        => $check_out,
                "room_type"      => $room_type,
                "room_no"        => $room_no,
                "reservation_no" => $reservation,
                "booking_price"  => $total_price,
                "company_id"     => $company_id,
            ],
        ];

        if ($payload["whatsapp"]) {

            $whatsappPayload = [
                'recipient'  => $payload["whatsapp"],
                'text'       => (new Controller)->prepareMessage($payload['fields'], "whatsapp", $payload["command"]),
                'company_id' => $company_id,
                "mediaUrl"   => $mediaUrl,
            ];
            WhatsappSender::dispatch($whatsappPayload);
        }

        if ($payload["email"]) {

            $emailPayload = [
                'recipient'  => $payload["email"],
                'text'       => (new Controller)->prepareMessage($payload['fields'], "email", $payload["command"]),
                'company_id' => $company_id,
                "heading"    => $heading,
                "mediaUrl"   => $mediaUrl,
                // "mediaUrl" => "https://backend.myhotel2cloud.com/vouchers/voucher_3_427.pdf",
            ];

            // Mail::to($payload["email"])->queue(new EmailDispatcherWithAttachment($emailPayload));
            EmailSender::dispatch($emailPayload);

        }
    }

    public function deleteBooking($id)
    {
        DB::beginTransaction();
        try {
            Booking::where('id', $id)->delete();
            Payment::where('booking_id', $id)->delete();
            Transaction::where('booking_id', $id)->delete();
            OrderRoom::where('booking_id', $id)->delete();
            BookedRoom::without(['postings', 'booking'])->where('booking_id', $id)->delete();
            Posting::where('booking_id', $id)->delete();
            DB::commit();
             return response()->json([
                'message' => 'Deleted booking successfully',
            ], 500);
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete booking',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function direchCheckIn(Request $request)
    {
        DB::beginTransaction();
        try {
            $request['customer_id'] = $this->customerStore($request->only(Customer::customerAttributes()));

            $data                          = [];
            $data                          = $request->only(Booking::bookingAttributes());
            $data['booking_date']          = date("Y-m-d");
            $data['payment_status']        = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
            $data['remaining_price']       = (float) $request->total_price - (float) $request->advance_price;
            $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
            $data['reservation_no']        = $this->getReservationNumber($data);
            $data['verified']              = Booking::VERIFICATION_REQUIRED;
            $data['booking_type']          = $request->booking_type ?? "room";

            $data['discount']    = $request->room_discount ?? 0;
            $data['total_extra'] = $request->room_extra_amount ?? 0;

            if ($request->filled('api_json_reference_number')) {
                $data['widget_confirmation_number'] = $request->api_json_reference_number;
            }

            if ($request->filled("payment_reference_id")) {
                $data['payment_reference_id'] = $request->payment_reference_id;
                $data['payment_response']     = json_encode($request->payment_response);
            }

            $booking = Booking::create($data);

            if ($booking) {

                (new Booking)->processFinancials($booking, $request);

                $data = [
                    'selectedRooms'     => $request->input('selectedRooms'),
                    'room_discount'     => $request->input('room_discount'),
                    'room_extra_amount' => $request->input('room_extra_amount'),
                    'booking_id'        => $booking->id,
                    'customer_id'       => $request['customer_id'],
                    'company_id'        => $request->company_id ?? 3,
                    'booking_status'    => BookedRoom::CHECKED_IN,
                ];

                StoreBookedRoomsJobForDirectCheckIn::dispatch($data);
            }

            DB::commit();

            $this->processNotification(Template::BOOKING_CREATE, "BOOKING CREATE", $request);

            sleep(2);

            return response()->json(['data' => $booking->id, 'booking_reservation_number' => $this->getReservationNumber($data), 'status' => true]);

            // all good
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'An error occurred. Please try again.' . $e->getMessage()]); // return a user-friendly error
        }
    }

     public function invoice($id)
    {
        $booking = Booking::with([
            'orderRooms',
            'customer:id,first_name,last_name,contact_no,city,state,zip_code,country,gst_number',
            'company:id,user_id,name,location,mol_id,logo',
            'company.user:id,email',
            'company.contact',
            'transactions',
            'transactions.paymentMode',
            'bookedRooms',
        ])->find($id);

        $company  = $booking->company;
        $customer = $booking->customer;

        $company->location = explode("\n", $company->location);

        $prefix = "INV-";

        if ($booking->gst_number || $booking?->customer?->source?->gst) {
            $prefix = 'GST-';
        }

        $previousCount = Booking::where('company_id', $booking->company_id)
            ->where('created_at', '<', $booking->created_at)
            ->count();

        $startFrom = 1000;

        $currentCount = $previousCount + $startFrom + 1;

        $invoice = str_pad($prefix . $currentCount, 4, '0', STR_PAD_LEFT);

        $lastPaymentModeId = $booking?->transactions?->value("payment_method_id");

        $orderRooms   = $booking->orderRooms;
        $transactions = $booking->transactions;
        $bookedRooms  = $booking->bookedRooms;

        $first_check_in_time  = $bookedRooms[0]["actual_check_in_time"] ?? "00:00";
        $first_check_out_time = $bookedRooms[0]["actual_check_out_time"] ?? "00:00";

        $first_check_in_date  = date('d M Y', strtotime($booking->check_in));
        $first_check_out_date = date('d M Y', strtotime($booking->check_out));

        $booking_date = date('d M Y', strtotime($booking->booking_date));

        $total_rooms = count($booking->bookedRooms ?? []) ?? 1;

        $roomTypes = array_unique(array_column($booking->bookedRooms->toArray(), 'room_type'));

        $room_types = implode(',', $roomTypes);

        $currency   = $company->currency ?? '₹';
        $company_id = $booking->company_id ?? 0;

        $booking = [
            "first_check_in_time"  => $first_check_in_time,
            "first_check_out_time" => $first_check_out_time,
            "first_check_in_date"  => $first_check_in_date,
            "first_check_out_date" => $first_check_out_date,
            "total_rooms"          => $total_rooms,
            "room_types"           => $room_types,
            "paid_amounts"         => $booking->paid_amounts ?? 0,
            "balance"              => $booking->balance ?? 0,
            "reservation_no"       => $booking->reservation_no ?? 0,
            "total_price"          => $booking->total_price ?? 0,
            "amtLetter"            => $this->amountToText($booking->total_price ?? 0),
        ];

        return Pdf::loadView("invoice.invoice_pdf", compact("company", "invoice", "customer", "booking", "orderRooms", "currency", "company_id"))
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function amountToText($amount)
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
        $text      = ucwords($formatter->format($amount));
        return $text . " Only";
    }
}
