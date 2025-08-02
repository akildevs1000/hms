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


        $config = [
            'default'      => config('mail.default'),
            'host'         => config('mail.mailers.smtp.host'),
            'port'         => config('mail.mailers.smtp.port'),
            'username'     => config('mail.mailers.smtp.username'),
            'password'     => config('mail.mailers.smtp.password'),
            'encryption'   => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name'    => config('mail.from.name'),
        ];

        echo "\n" . (lightDump(["SMTP Settings Info:", $config])) . "\n";

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
