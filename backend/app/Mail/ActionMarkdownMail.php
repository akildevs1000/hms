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

    public $id;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($body, $subject, $id)
    {
        $this->body = $body;
        $this->subject = $subject;
        $this->id = $id;
    }

    public function build()
    {
        $result = $this->subject($this->subject)
            ->markdown('emails.action_mail')
            ->with(['subject' => $this->subject, 'body' => $this->body]);

        if ($this->id) {
            $output = $this->getPdfContent($this->id)->output();
            $result->attachData($output, 'quotation.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $result;
    }

    public function getPdfContent($id)
    {
        $quotation = Quotation::with("company", "customer")->where("type", "room")->findOrFail($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));
        // $logoPath = urldecode("https://backend.myhotel2cloud.com/upload/1673109140.jpg"); // Replace with your dynamic URL
        // $logoData = base64_encode(file_get_contents($logoPath));
        // $quotation->company->logo = 'data:image/jpeg;base64,' . $logoData;
        return $pdfContent = Pdf::loadView('quotation.room', compact("quotation"))
            ->setPaper('a4', 'portrait');
    }
}
