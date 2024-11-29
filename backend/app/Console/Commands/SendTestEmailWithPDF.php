<?php

namespace App\Console\Commands;

use App\Mail\ActionMarkdownMail;
use App\Mail\BasicEmailWithPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmailWithPDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-pdf {email} {quotationId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email with a PDF attachment';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // // Get the email address from the command argument
        $email = $this->argument('email');
        $quotationId = $this->argument('quotationId');

        // // Prepare data for the email
        // $data = [
        //     'name' => 'John Doe',
        //     'message' => 'This is a sample message.',
        // ];

        // // Generate the PDF
        // // $pdf = Pdf::loadView('emails.pdf_template', $data);

        // // Send the email with PDF attachment
        // Mail::to($email)->send(new BasicEmailWithPDF($data, $quotationId));

        // // Output a success message to the console
        // $this->info("Email with PDF sent to {$email}");
        
        // return;

        try {
            // Send the test email
            Mail::to($email)->send(new ActionMarkdownMail(
                'This is the body text of the test email.',
                'Test Email Subject',
                $quotationId
            ));

            $this->info('Test email sent successfully to ' . $email);
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }

        return 0;
    }
}
