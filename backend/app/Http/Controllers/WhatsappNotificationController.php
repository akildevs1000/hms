<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsappNotificationController extends Controller
{
    public function PostingNotification($data)
    {
        $company_id  = $data['company_id'];
        $instance_id  = "";
        $access_token = "";
        $location     = "";
        $video        = "";
        $msg          = "";
        $date     = date('d-M-y', strtotime($data['posting_date']));
        $time     = date('H:i', strtotime($data['posting_date']));
        $item  = $data['item'];
        $tax  = $this->getAmountFormat($data['tax']);
        $single_amt  = $this->getAmountFormat($data['single_amt']);
        $amount  = $this->getAmountFormat($data['amount']);
        $amount_with_tax  = $data['amount_with_tax'];
        $qty  = $data['qty'];
        $room_no = $data['room_no'];

        if ($company_id == 1) {
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
            $video        = "https://www.youtube.com/watch?v=tF-8q991Prw&ab_channel=HYDERSPARK-GroupOfHotels";
        }


        $msg .= "*Room No:-*  $room_no, \n";
        $msg .= "*Date:-*  $date, \n";
        $msg .= "*Time:-*  $time, \n";
        $msg .= "\n";
        $msg .= "------------ \n";
        $msg .= "Your orders, \n";
        $msg .= "------------ \n";
        $msg .= "*Item  :*  $item, \n";
        $msg .= "*Amount:*  $single_amt, \n";
        $msg .= "*QTY   :*  $qty, \n";
        $msg .= "*Total :*  $amount, \n";
        $msg .= "*Tax   :*  $tax, \n";
        $msg .= "*Total with Amount:-*  $amount_with_tax, \n";
        $msg .= "------------ \n";
        $msg .= "\n";
        $msg .= "Further information can be obtained by Hotel Manager Mr. Ansari, 89402 30003.\n";

        $data = [
            'to'           => env('COUNTRY_CODE') . $data['whatsapp'],
            'message'      => $msg,
            'company'      => $company ?? false,
            'instance_id'  => $instance_id,
            'access_token' => $access_token,
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
            $instance_id  = "KODAI_INSTANCE_ID";
            $access_token = "KODAI_ACCESS_TOKEN";
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
            $location     = "https://goo.gl/maps/bNznm2Z4pbxo2ZJw9";
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
}
