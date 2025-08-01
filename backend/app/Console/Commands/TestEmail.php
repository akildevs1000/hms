<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class TestEmail extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Send a test email with a fixed PNG attachment';

    public function handle()
    {
        $email = $this->argument('email');

        $filePath = "https://backend.myhotel2cloud.com/vouchers/voucher_3_427.pdf";

        // Send email
        Mail::raw('This is a test email from Laravel.', function ($message) use ($email, $filePath) {
            $message->to($email)
                ->subject('Test Email')
                ->attach($filePath);
        });

        $this->info("Test email sent to: $email with attachment: $filePath");
    }
}
