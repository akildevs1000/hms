<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActionMarkdownMail;
use App\Models\Customer;
use App\Models\Template;

class SendFestivalMessage extends Command
{
    protected $signature = 'email:send-festival-message';
    protected $description = 'Send festival messages to customers';

    public function handle()
    {
        $customers = Customer::whereNotNull('email')->get();

        foreach ($customers as $customer) {
            $found = Template::where(["medium" => "email", "action_id" => Template::FESTIVAL_MESSAGE])->first();

            if ($found) {
                $subject = "Happy Festival " . $customer->full_name . "!";

                $body = str_replace(
                    ['[title]', '[full_name]', '[festival_name]'],
                    [
                        $customer->title,
                        $customer->full_name,
                    ],
                    $found->body
                );

                if ($customer->email) {
                    Mail::to($customer->email)->queue(new ActionMarkdownMail($body, $subject));
                    info("Festival message sent to {$customer->email}");
                } else {
                    info("no mail sent for email:send-birthday-wish, customer email not defined.");
                }
            }
        }
    }
}
