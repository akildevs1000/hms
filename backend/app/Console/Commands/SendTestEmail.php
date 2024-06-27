<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-test';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email';
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $to = $this->ask("email","akildevs1000@gmail.com");

        $subject = 'Test Email';
        
        $body = 'This is a test email sent from the Laravel command.';

        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)
                    ->subject($subject);
        });

        $this->info('Test email sent successfully!');
    }
}
