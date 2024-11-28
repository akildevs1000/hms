<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActionMarkdownMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $body;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($body, $subject)
    {
        $this->body = $body;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('emails.action_mail') // Create this view file
            ->with(['subject' => $this->subject, 'body' => $this->body]);
    }
}
