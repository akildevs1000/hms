<?php

namespace App\Console\Commands\Automation;

use App\Http\Controllers\Controller;
use App\Jobs\EmailSender;
use App\Jobs\WhatsappSender;
use App\Models\Booking;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OnCheckoutDateReminder extends Command
{
    protected $signature = 'on_checkout_date_reminder';

    protected $description = 'Send OnCheckoutDateReminder to customer';

    protected $templates = [];

    protected $tags = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookings = Booking::with("customer:id,title,first_name,last_name,email,whatsapp")
            ->whereDate('check_in', Carbon::now()->format('Y-m-d'))
            // ->take(1)
            ->get();

        if (count($bookings) == 0) {
            $this->info("no record found for on_checkout_date_reminder");
            return;
        }

        foreach ($bookings as $booking) {

            $payload = [
                "command" => Template::ON_CHECKOUT_DATE_CHECKOUT_REMINDER,
                "heading" => "ON_CHECKOUT_DATE_REMINDER",
                "company_id" => $booking->company_id,
                "whatsapp" => $booking->customer->whatsapp ?? null,
                "email" => $booking->customer->email ?? null,

                // "whatsapp" => "971554501483",
                // "email" => "francisgill1000@gmail.com",

                "fields" => [
                    'title' => $booking->customer->title,
                    'full_name' => $booking->customer->full_name,
                    'from_date' => date('d-M-y', strtotime($booking->arrival_date)),
                    'to_date' => date('d-M-y', strtotime($booking->departure_date)),
                ]
            ];

            if ($payload["whatsapp"]) {
                WhatsappSender::dispatch([
                    'recipient' => $payload["whatsapp"],
                    'text' => (new Controller)->prepareMessage($payload['fields'], "whatsapp", $payload["command"]),
                    'company_id' => $payload["company_id"],
                ]);
            }

            if ($payload["email"]) {
                EmailSender::dispatch([
                    'recipient' => $payload["email"],
                    'text' => (new Controller)->prepareMessage($payload['fields'], "email", $payload["command"]),
                    'company_id' => $payload["company_id"],
                    'heading' => $payload["heading"],
                ]);
            }

            echo json_encode($payload, JSON_PRETTY_PRINT);
        }
    }
}
