<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        // $pdf = $this->generateRoomQuotationPDF($this->quotationId);

        $result = $this
            ->from("hydersparkthotel@gmail.com")
            ->subject('Subject Here')
            ->markdown('emails.action_mail')
            ->with(['subject' => "test subject", 'body' => "dontsdlfkj"]);

        // $result->attachData($pdf->output(), 'quotation.pdf', [
        //     'mime' => 'application/pdf',
        // ]);

        return $result;
    }

    private function generateRoomQuotationPDF($id)
    {
        $quotation = Quotation::with("company", "customer")->where("type", "room")->findOrFail($id);
        $quotation->total_no_of_nights = array_sum(array_column($quotation->items, "no_of_nights"));
        $quotation->total_no_of_rooms = array_sum(array_column($quotation->items, "no_of_rooms"));
        $quotation->room_types = join(",", array_column($quotation->items, "room_type"));
        $logoPath = urldecode($quotation->company->logo); // Replace with your dynamic URL
        $logoData = base64_encode(file_get_contents($logoPath));
        $quotation->company->logo = 'data:image/jpeg;base64,' . $logoData;
        return Pdf::loadView('quotation.room', compact("quotation"))
            ->setPaper('a4', 'portrait');
    }
}
