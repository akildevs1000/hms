<?php

namespace App\Console\Commands;

use App\Jobs\SendTestEmailJob;
use App\Mail\ActionMarkdownMail;
use App\Mail\TestMail;
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
        $to = $this->ask("email", "akildevs1000@gmail.com");
        $subject = $this->ask("subject", "subject");
        $body = $this->ask("body", "body");
        Mail::to($to)->queue(new TestMail($subject, $body));
        $this->info('Test email job sent successfully!');
    }
}
