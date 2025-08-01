<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDispatcherWithAttachment;

class TestEmailWithAttachment extends Command
{
    protected $signature = 'email:test-custom {email}';
    protected $description = 'Test email with attachment using custom mailable';

    public function handle()
    {
        $recipient = $this->argument('email');
        $request = [
            'recipient' => $recipient,
            'text' => 'This is a test message body with attachment.',
            'heading' => 'Test Email Subject',
            'mediaUrl' => 'https://backend.myhotel2cloud.com/vouchers/voucher_3_427.pdf',
        ];

        Mail::to($recipient)->queue(new EmailDispatcherWithAttachment($request));

        $this->info("Email dispatched to queue for: $recipient");
    }
}
