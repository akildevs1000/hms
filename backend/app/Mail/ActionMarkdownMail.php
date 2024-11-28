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
    private $quotationId;

    /**
     * Create a new message instance.
     *
     * @param string $body
     * @param string $subject
     * @param int $quotationId
     * @return void
     */
    public function __construct($body, $subject, $quotationId)
    {
        $this->body = $body;
        $this->subject = $subject;
        $this->quotationId = $quotationId;
    }

    public function build()
    {
        // $pdfContent = $this->generateRoomQuotationPDF($this->quotationId);
        $pdf = Pdf::loadView('emails.quotation');

        return $this->subject($this->subject)
            ->view('emails.quotation')
            ->attachData($pdf->output(), 'example.pdf', [
                'mime' => 'application/pdf',
            ]);

        // ->attachData($pdfContent, 'quotation.pdf', [
        //     'mime' => 'application/pdf',
        // ]);
    }

    /**
     * Generate the PDF for the given quotation ID.
     *
     * @param int $id
     * @return string
     */
    private function generateRoomQuotationPDF($id)
    {
        $quotation = Quotation::with("company", "customer")->where("type", "room")->findOrFail($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));
        // Generate and return PDF content
        return Pdf::loadView('quotation.room', compact("quotation"))
            ->setPaper('a4', 'portrait')
            ->output();
    }
}
