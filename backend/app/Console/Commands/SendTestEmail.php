<?php

namespace App\Console\Commands;

use App\Jobs\SendTestEmailJob;
use Illuminate\Console\Command;

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
        $to = $this->ask("email", "akildevs1000@gmail.com");
        $subject = 'Test Email';
        $body = 'This is a test email sent from the Laravel command.';
        SendTestEmailJob::dispatch($to, $subject, $body);
        $this->info('Test email job dispatched successfully!');
    }
}
