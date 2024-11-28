<?php

namespace App\Mail;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActionMarkdownMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $body;
    public $subject;
    private $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($body, $subject, $pdf = null)
    {
        $this->body = $body;
        $this->subject = $subject;
        $this->pdf = $pdf; // Optional PDF attachment
    }

    public function build()
    {
        $email = $this->subject($this->subject)
            ->markdown('emails.action_mail') // Ensure this view exists
            ->with(['subject' => $this->subject, 'body' => $this->body]);

        if ($this->pdf) {
            $email->attachData($this->pdf, 'quotation.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
