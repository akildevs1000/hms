<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BirthdayWishEmailCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $recipient = $this->request['recipient'] ?? null;
        $messageBody = $this->request['text'] ?? null;

        if ($recipient && $messageBody) {
            Mail::raw($messageBody, function ($message) use ($recipient) {
                $message->to($recipient)
                        ->subject('Happy Birthday!');
            });
        }
    }
}
