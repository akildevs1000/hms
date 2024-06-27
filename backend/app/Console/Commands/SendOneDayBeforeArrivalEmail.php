<?php

namespace App\Console\Commands;

use App\Mail\ActionMarkdownMail;
use App\Models\Booking;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOneDayBeforeArrivalEmail extends Command
{
    protected $signature = 'email:send-one-day-before-arrival';
    protected $description = 'Send an email reminder one day before arrival';

    public function handle()
    {
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

        $bookings = Booking::whereHas('customer', function ($query) {
            $query->whereNotNull('email');
        })->with("customer:id,title,first_name,last_name,email,whatsapp")->whereDate('check_in', $tomorrow)->get();

        if (count($bookings) == 0) {
            $this->info("no record found for email:send-one-day-before-arrival.");
            info("no record found for email:send-one-day-before-arrival.");
            return;
        }

        foreach ($bookings as $booking) {
            $found = Template::where(["medium" => "email", "action_id" => Template::ONE_DAY_BEFORE_ARRIVAL])->first();

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

                Mail::to($booking->customer->email)->queue(new ActionMarkdownMail($body, $subject));
                $this->info("mail sent for email:send-one-day-before-arrival.\n");
                info("mail sent for email:send-one-day-before-arrival.");
            }
        }
    }
}
