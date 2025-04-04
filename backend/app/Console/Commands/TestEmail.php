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

        // Fixed URL for the invoice
        $attachmentUrl = "https://backend.myhotel2cloud.com/invoices/invoice_1743693202.png";

        // Extract filename and set storage path
        $fileName = basename(parse_url($attachmentUrl, PHP_URL_PATH));
        $directoryPath = public_path("invoices");
        $filePath = "$directoryPath/$fileName";

        // Ensure the directory exists
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        // Download the file if it does not exist
        if (!file_exists($filePath)) {
            $fileContents = Http::withoutVerifying()->get($attachmentUrl)->body();
            if (file_put_contents($filePath, $fileContents)) {
                $this->info("File downloaded: $filePath");
            } else {
                $this->error("Failed to download attachment.");
                return;
            }
        }

        // Send email
        Mail::raw('This is a test email from Laravel.', function ($message) use ($email, $filePath) {
            $message->to($email)
                ->subject('Test Email')
                ->attach($filePath);
        });

        $this->info("Test email sent to: $email with attachment: $filePath");
    }
}
