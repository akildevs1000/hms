<?php

namespace App\Console\Commands;

use App\Mail\ActionMarkdownMail;
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
        $email = $this->argument('email');
        $quotationId = $this->argument('quotationId');

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
