<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActionMarkdownMail;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Template;

class SendBirthdayWish extends Command
{
    protected $signature = 'email:send-birthday-wish';
    protected $description = 'Send birthday wishes to customers';

    public function handle()
    {
        // $today = Carbon::now()->format('m-d');

        // $customers = Customer::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [$today])->get();

        // for sqlite
        $customers = Customer::whereNotNull('email')->whereRaw('strftime("%d", dob) = ?', [date("d")])->get();


        foreach ($customers as $customer) {
            $found = Template::where(["medium" => "email", "action_id" => Template::BIRTHDAY_WISH])->first();

            if ($found) {
                $subject = "Happy Birthday " . $customer->first_name . "!";

                $body = str_replace(
                    ['[title]', '[full_name]'],
                    [
                        $customer->title,
                        $customer->full_name,
                    ],
                    $found->body
                );

                if ($customer->email) {
                    Mail::to($customer->email)->queue(new ActionMarkdownMail($body, $subject));
                    info("mail sent for email:send-birthday-wish.");
                } else {
                    info("no mail sent for email:send-birthday-wish, customer email not defined.");
                }
            }
        }
    }
}
