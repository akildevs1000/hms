<?php

namespace App\Console\Commands;

use App\Mail\ActionMarkdownMail;
use App\Models\Booking;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendArrivalDayEmail extends Command
{
    protected $signature = 'email:send-arrival-day';
    protected $description = 'Send an email reminder on the current arrival day';

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        $bookings = Booking::whereHas('customer', function ($query) {
            $query->whereNotNull('email');
        })->with("customer:id,title,first_name,last_name,email,whatsapp")->whereDate('check_in', $today)->get();

        if (count($bookings) == 0) {
            $this->info("no record found for email:send-arrival-day.");
            info("no record found for email:send-arrival-day.");
            return;
        }

        foreach ($bookings as $booking) {
            $found = Template::where(["medium" => "email", "action_id" => Template::ON_ARRIVAL_DATE])->first();

            if ($found) {
                $subject = $found->name;

                $body = str_replace(
                    ['[title]', '[full_name]', '[from_date]', '[to_date]'],
                    [
                        $booking->customer->title,
                        $booking->customer->full_name,
                        date('d-M-y', strtotime($booking->arrival_date)),
                        date('d-M-y', strtotime($booking->departure_date)),
                    ],
                    $found->body
                );

                if ($booking->customer->email) {
                    Mail::to($booking->customer->email)->queue(new ActionMarkdownMail($body, $subject));
                    info("mail sent for email:send-arrival-day.");
                } else {
                    info("no mail sent for email:send-arrival-day, customer email not defined.");
                }
            }
        }
    }
}
