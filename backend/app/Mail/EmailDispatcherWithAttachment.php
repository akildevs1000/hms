<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// <-- add

class EmailDispatcherWithAttachment extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function build()
    {
        $recipient   = $this->request['recipient'] ?? null;
        $messageBody = $this->request['text'] ?? null;
        $heading     = $this->request['heading'] ?? 'No Subject';
        $mediaUrl    = $this->request['mediaUrl'] ?? null;

        $email = $this->subject($heading);

        if (isset($mediaUrl)) {
            $email->attach($mediaUrl);
        }

        if ($recipient && $messageBody) {
            echo "\n" . lightDump($this->request) . "\n";
            return $email->view('booking.empty')->with(["body" => $messageBody]);
        }

        return $email; // Return something even if fields are missing
    }

}
