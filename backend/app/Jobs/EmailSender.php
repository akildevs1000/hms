<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $recipient   = $this->request['recipient'] ?? null;
        $messageBody = $this->request['text'] ?? null;
        $heading     = $this->request['heading'] ?? null;
        $mediaUrl    = $this->request['mediaUrl'] ?? null;

        if ($recipient && $messageBody) {
            echo "\n" . lightDump($this->request) . "\n";
            Mail::raw($messageBody, function ($message) use ($recipient, $heading, $mediaUrl) {
                $message->to($recipient)
                    ->subject($heading ?? 'Happy Birthday!');

                if ($mediaUrl) {
                    $message->attach($mediaUrl);
                }
            });
        }
    }
}
