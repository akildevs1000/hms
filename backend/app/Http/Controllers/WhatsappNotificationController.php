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
        $title         = $data['title'];
        $guest = ucfirst($data['guest']) ?? 'Guest';
        $instance_id = $company->whatsapp_instance_id;

        $msg .= "Dear $title. $guest, \n";
        $msg .= "\n";
        $msg .= "Room No :  $room_no \n";
        $msg .= "Date :  $date \n";
        $msg .= "Time :  $time \n";
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
        $title = ucfirst($customer['title']) ?? 'Mr';
        $checkOut     = date('d-M-y H:i', strtotime($booking->check_out));
        $company      = $booking->company;

        $location    = $company->map;
        $instance_id = $company->whatsapp_instance_id;
        $comName     = $company->company_code;
        $video       = $company->video;

        $msg .= "Dear $title. $customerName, \n";
        $msg .= "Welcome to  $comName\n";

        $msg .= "\n";
        $msg .= "--------------- \n";
        $msg .= "Free WiFi \n";
        $msg .= "--------------- \n";
        $msg .= "Username -  HYDERSPARK \n";
        $msg .= "PASSWORD -  Park@1234 \n";
        $msg .= "--------------- \n";
        $msg .= "Intercom \n";
        $msg .= "--------------- \n";
        $msg .= "Reception  :  100 \n";
        $msg .= "Restaurant  :  222 \n";
        $msg .= "House keeping : 100 \n";
        $msg .= "--------------- \n";
        $msg .= "Manager on Duty  \n";
        $msg .= "Mr. Ansari / 89402 30003  \n";
        $msg .= "--------------- \n";
        $msg .= "\n";
        $msg .= "Have a pleasant Stay with us !\n";

        // $msg .= is_null($location) ? '' : "Google Map  $location\n";
        // $msg .= "\n";
        // $msg .= is_null($video) ? '' : "More  $video\n";

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




        $msg .= "Dear $title. $customerName, \n";
        $msg .= " Thank you  for your stay with us \n";

        $msg .= "\n";
        $msg .= "--------------- \n";
        $msg .= "Rev. No : $booking->reservation_no \n";
        $msg .= "No. of Rooms : $booking->rooms \n";
        $msg .= "No. of Days : $booking->total_days \n";
        $msg .= "Check In : $booking->check_in \n";
        $msg .= "Check Out : $booking->check_out \n";
        $msg .= "--------------- \n";
        $msg .= "Your Bill Amount :  ₹$booking->total_price \n";
        $msg .= "You paid advance :  ₹$booking->paid_amounts \n";
        $msg .= "Your Balance Amount :  ₹$booking->balance \n";
        $msg .= "--------------- \n";

        $msg .= "Please write your review \n";
        $msg .= is_null($review) ? '' : "More  $review\n";

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


        $msg .= "Dear $title. $customerName, \n";
        $msg .= "Welcome to  $comName\n";
        $msg .= " Thank you  for your room reservation \n";

        $msg .= "\n";
        $msg .= "--------------- \n";
        $msg .= "Rev. No : $booking->id \n";
        $msg .= "No. of Rooms : $booking->total_days \n";
        $msg .= "No. of Days : $numberOfRooms \n";
        $msg .= "Check In : $checkIn \n";
        $msg .= "Check Out : $checkOut \n";
        $msg .= "--------------- \n";
        $msg .= "Your Bill Amount :  ₹$totalAmount \n";
        $msg .= "You paid advance :  ₹$advanceAmount \n";
        $msg .= "Your Balance Amount :  ₹$remainingAmount \n";
        $msg .= "--------------- \n";
        $msg .= "\n";
        if ($advanceAmount <= 0) {
            $msg .= "Please pay Advance to confirm your booking\n";
        }
        $msg .= "\n";
        $msg .= is_null($location) ? '' : "Google Map  $location\n";
        $msg .= "\n";
        $msg .= "\n";
        $msg .= "Further information can   be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";
        // $msg .= is_null($video) ? '' : "More  $video\n";

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


    public function getPayMode($mode = "")
    {
        return match ($mode) {
            1 => 'Cash',
            2 => 'Card',
            3 => 'Online',
            4 => 'Bank',
            5 => 'UPI',
            6 => 'Cheque',
        };
    }
}
