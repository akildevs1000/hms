<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class BasicEmailWithPDF extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $quotationId;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $quotationId)
    {
        $this->data = $data;
        $this->quotationId = $quotationId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->generateRoomQuotationPDF($this->quotationId);

        return $this->subject('Subject Here')
            ->view('emails.quotation')
            ->attachData($pdf->output(), 'quotation.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    private function generateRoomQuotationPDF($id)
    {
        $quotation = Quotation::with("company", "customer")->where("type", "room")->findOrFail($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));
        // Generate and return PDF content
        return Pdf::loadView('quotation.room', compact("quotation"))
            ->setPaper('a4', 'portrait');
    }
}
