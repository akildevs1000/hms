<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $subject, public $body) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->subject;
        $body = $this->body;

        //$this->subject($subject);

        info("subject = $subject, body = $body");

        // foreach ($this->model->reports as $file){
        //     $this->attach(storage_path("app/$company_id/$file"));
        // }

        return $this->view('emails.report')->with(["body" => "body"]);
    }
}
