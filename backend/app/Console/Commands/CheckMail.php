<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfMail;  // Ensure this points to the correct Mail class

class CheckMail extends Command
{
    // Command name
    protected $signature = 'mail:check';

    // Command description
    protected $description = 'Check if the mail system is working by sending a test email.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // // Test email address, replace with a valid one
        $testEmail = 'francisgill1000@gmail.com';
        // $testImagePath = "https://backend.myhotel2cloud.com/invoices/invoice_1743693202.png"; // Path to a test image

        try {
            // Attempt to send the email
            Mail::to($testEmail)->send(new PdfMail());

            $this->info('Test email sent successfully!');
        } catch (\Exception $e) {
            // Catch and display any errors
            $this->error('Failed to send test email: ' . $e->getMessage());
        }
    }
}
