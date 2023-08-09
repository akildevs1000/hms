<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuditReportMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (isset($this->data['file'])) {
            $this->attach($this->data['file']);
        } else {
            if (isset($this->data['files'])) {
                foreach ($this->data['files'] as $file) {

                    $this->attach($file);
                }
            }
        }

        return $this->view('emails.audit.audit')->with(["body" => $this->data['body'], "company" => $this->data['company'], 'date' => $this->data['date']]);
    }
}
