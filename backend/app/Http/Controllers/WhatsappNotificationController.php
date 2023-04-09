<?php

namespace App\Http\Controllers;

class WhatsappNotificationController extends Controller
{
    public function PostingNotification($data)
    {
        $company_id      = $data['company_id'];
        $instance_id     = "";
        $access_token    = "";
        $msg             = "";
        $date            = date('d-M-y', strtotime($data['posting_date']));
        $time            = date('H:i', strtotime($data['posting_date']));
        $item            = $data['item'];
        $tax             = $this->getAmountFormat($data['tax']);
        $single_amt      = $this->getAmountFormat($data['single_amt']);
        $amount          = $this->getAmountFormat($data['amount']);
        $amount_with_tax = $data['amount_with_tax'];
        $qty             = $data['qty'];
        $room_no         = $data['room_no'];
        $company         = $data['company'];

        $instance_id = $company->whatsapp_instance_id;

        $msg .= "Room No:-  $room_no \n";
        $msg .= "Date:-  $date \n";
        $msg .= "Time:-  $time \n";
        $msg .= "\n";
        $msg .= "------------ \n";
        $msg .= "Your orders \n";
        $msg .= "------------ \n";
        $msg .= "Item  :  $item \n";
        $msg .= "Unit price:  ₹ $single_amt \n";
        $msg .= "QTY   :  $qty \n";
        $msg .= "Sub total :  ₹ $amount \n";
        $msg .= "Tax   :  ₹ $tax \n";
        $msg .= "Total:  ₹ $amount_with_tax \n";
        $msg .= "------------ \n";
        $msg .= "\n";
        $msg .= "Thank you for your business\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $data['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
            'type'         => 'posting',
        ];
        (new WhatsappController)->sentNotification($data);
    }

    public function advancePayingNotification($booking, $customer, $amt, $payMode = 0)
    {
        $instance_id  = "";
        $access_token = "";
        $msg          = "";
        $customerName = ucfirst($customer['first_name']) ?? 'Guest';
        $company      = $booking->company;
        $date         = date('d-M-y');
        $time         = date('H:i');
        $instance_id  = $company->whatsapp_instance_id;

        $payMode = $this->getPayMode($payMode) ?? "";

        $msg .= "Dear $customerName, \n";

        $msg .= "Room No:-  $booking->rooms \n";
        $msg .= "Date:-  $date \n";
        $msg .= "Time:-  $time \n";
        $msg .= "\n";
        $msg .= "------------ \n";
        $msg .= "Payment Received \n";
        $msg .= "------------ \n";
        $msg .= "Amount:  ₹ $amt \n";
        $msg .= "Payment By   :  $payMode \n";
        $msg .= "Balance :  ₹ $booking->balance \n";
        $msg .= "------------ \n";
        $msg .= "\n";
        $msg .= "Thank you for your business\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
            'type'         => 'checkin',
        ];
        (new WhatsappController)->sentNotification($data);
    }
    public function checkInNotification($booking, $customer)
    {
        $instance_id  = "";
        $access_token = "";
        $comName      = "";
        $location     = "";
        $video        = "";
        $msg          = "";
        $customerName = ucfirst($customer['first_name']) ?? 'Guest';
        $checkOut     = date('d-M-y H:i', strtotime($booking->check_out));
        $company      = $booking->company;

        $location    = $company->map;
        $instance_id = $company->whatsapp_instance_id;
        $comName     = $company->company_code;
        $video       = $company->video;

        $msg .= "Dear $customerName, \n";

        $msg .= "Welcome to  $comName, We are pleased to have you as our guest. Enjoy your stay \n";
        $msg .= "\n";
        $msg .= "We hope that your stay with us is both comfortable and memorable, \n";
        $msg .= "If you have any questions or concerns, please don't hesitate to reach out to our staff, \n";
        $msg .= "We are here to make your experience at $comName an exceptional one, \n";
        $msg .= "\n";

        $msg .= "Stay connected during your stay with our complimentary high-speed Wi-Fi, \n";
        $msg .= "Enjoy your time in  $comName, \n";
        $msg .= "\n";

        $msg .= "USER NAME - HYDERSPARK, \n";
        $msg .= "PASSWORD - Park@1234, \n";
        $msg .= "Check Out  $checkOut, \n";

        $msg .= "Further information can be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        $msg .= "\n";
        $msg .= "\n";

        $msg .= is_null($location) ? '' : "Google Map  $location\n";
        $msg .= "\n";
        $msg .= is_null($video) ? '' : "More  $video\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
            'type'         => 'checkin',
        ];
        (new WhatsappController)->sentNotification($data);
    }

    public function checkOutNotification($booking, $customer)
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

        $company = $booking->company;

        $location    = $company->map;
        $instance_id = $company->whatsapp_instance_id;
        $comName     = $company->company_code;
        $video       = $company->video;
        $review      = $company->review;

        $msg .= "Dear $title $customerName, \n";
        $msg .= "\n";
        $msg .= "Thank you for your stay \n";
        $msg .= "We hope that your experience with us was great, \n";
        $msg .= "We had a great time serving you, \n";
        $msg .= "we look forward to having you back with us soon, \n";
        $msg .= "Safe Travels, \n";
        $msg .= "\n";

        $msg .= "Further information can be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        $msg .= "\n";
        $msg .= "please make your review $review";
        $msg .= "\n";
        $msg .= "\n";

        $msg .= is_null($location) ? '' : "Google Map  $location\n";
        $msg .= "\n";
        $msg .= is_null($video) ? '' : "More  $video\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
            'type'         => 'checkout',
        ];
        (new WhatsappController)->sentNotification($data);
    }

    public function whatsappNotification($booking, $rooms, $customer, $type)
    {
        $instance_id     = "";
        $access_token    = "";
        $msg             = "";
        $numberOfRooms   = count($rooms);
        $customerName    = ucfirst($customer['first_name']) ?? 'Guest';
        $title           = ucfirst($customer['title']) ?? ' ';
        $checkIn         = date('d-M-y', strtotime($booking->check_in));
        $checkOut        = date('d-M-y H:i', strtotime($booking->check_out));
        $totalAmount     = $booking->total_price;
        $advanceAmount   = $booking->advance_price;
        $remainingAmount = $booking->remaining_price;
        $company         = $booking->company;

        $location    = $company->map;
        $instance_id = $company->whatsapp_instance_id;
        $comName     = $company->company_code;
        $video       = $company->video;

        $msg .= "Dear $title $customerName, \n";
        $msg .= "Welcome to  $comName, \n";
        $msg .= "Your booking reference number, $booking->id, \n";
        $msg .= "Number of Rooms, $numberOfRooms, \n";
        $msg .= "from, $checkIn to $checkOut, \n";
        $msg .= "Your total bill is ₹$totalAmount \n";
        $msg .= "You paid advance ₹$advanceAmount \n";
        $msg .= "Your remaining amount is ₹$remainingAmount \n";

        if ($advanceAmount <= 0) {
            $msg .= "Please pay Advance to confirm your booking  .\n";
        }

        $msg .= "\n";
        $msg .= "Further information can   be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        $msg .= "\n";
        $msg .= "\n";

        $msg .= is_null($location) ? '' : "Google Map  $location\n";
        $msg .= "\n";
        $msg .= is_null($video) ? '' : "More  $video\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
            'type'         => 'booking',
        ];
        (new WhatsappController)->sentNotification($data);
    }

    // public function bookingNotification($booking, $rooms, $customer, $type)
    // {
    //     $instance_id     = "";
    //     $access_token    = "";
    //     $msg             = "";
    //     $numberOfRooms   = count($rooms);
    //     $customerName    = ucfirst($customer['first_name']) ?? 'Guest';
    //     $title           = ucfirst($customer['title']) ?? ' ';
    //     $checkIn         = date('d-M-y', strtotime($booking->check_in));
    //     $checkOut        = date('d-M-y H:i', strtotime($booking->check_out));
    //     $totalAmount     = $booking->total_price;
    //     $advanceAmount   = $booking->advance_price;
    //     $remainingAmount = $booking->remaining_price;
    //     $company         = $booking->company;

    //     $location    = $company->map;
    //     $instance_id = $company->whatsapp_instance_id;
    //     $comName     = $company->company_code;
    //     $video       = $company->video;

    //     $msg .= "Dear $title $customerName, \n";
    //     $msg .= "Welcome to  Hyders park, \n";
    //     $msg .= "-------------- \n";
    //     $msg .= "Free WiFi \n";
    //     $msg .= "-------------- \n";
    //     $msg .= "from, $checkIn to $checkOut, \n";
    //     $msg .= "Your total bill is ₹$totalAmount \n";
    //     $msg .= "You paid advance ₹$advanceAmount \n";
    //     $msg .= "Your remaining amount is ₹$remainingAmount \n";

    //     if ($advanceAmount <= 0) {
    //         $msg .= "Please pay Advance to confirm your booking  .\n";
    //     }

    //     $msg .= "\n";
    //     $msg .= "Further information can   be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
    //     $msg .= "\n";
    //     $msg .= "\n";

    //     $msg .= is_null($location) ? '' : "Google Map  $location\n";
    //     $msg .= "\n";
    //     $msg .= is_null($video) ? '' : "More  $video\n";

    //     $data = [
    //         'to'           => env('COUNTRY_CODE') . $customer['whatsapp'],
    //         'message'      => $msg,
    //         'company'      => $company ?? false,
    //         'instance_id'  => $instance_id,
    //         'access_token' => $access_token,
    //         'type'         => 'booking',
    //     ];
    //     (new WhatsappController)->sentNotification($data);
    // }

    public function getPayMode($mode = "")
    {
        return match($mode) {
            1 => 'Cash',
            2 => 'Card',
            3 => 'Online',
            4 => 'Bank',
            5 => 'UPI',
            6 => 'Cheque',
        };
    }
}
