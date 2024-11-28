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
        // Generate PDF inside the build method

        $result = $this->subject($this->subject)
            ->markdown('emails.action_mail') // Ensure this view exists
            ->with(['subject' => $this->subject, 'body' => $this->body]);

        if ($this->quotationId) {
            $pdfContent = $this->generateRoomQuotationPDF($this->quotationId);

            $result->attachData($pdfContent, 'quotation.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return  $result;
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


        $pdf = Pdf::loadView('quotation.room', compact("quotation"))->setPaper('a4', 'portrait');

        // Save the PDF locally for debugging
        file_put_contents(storage_path('app/test_quotation.pdf'), $pdf->output());

        return $pdf->output();

        // Generate and return PDF content
        return Pdf::loadView('quotation.room', compact("quotation"))
            ->setPaper('a4', 'portrait')
            ->output();
    }
}
