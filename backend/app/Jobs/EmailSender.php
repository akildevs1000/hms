<?php

namespace App\Jobs;

use App\Services\MailConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        (new MailConfigService)->setMailConfigForCompany($this->request['company_id'] ?? 0);

        $recipient   = $this->request['recipient'] ?? null;
        $messageBody = $this->request['text'] ?? null;
        $heading     = $this->request['heading'] ?? null;
        $mediaUrl    = $this->request['mediaUrl'] ?? null;

        if ($recipient && $messageBody) {

            Mail::send([], [], function ($message) use ($recipient, $heading, $mediaUrl, $messageBody) {

                $message->setBody($messageBody, 'text/html');
                $message->to($recipient)
                    ->subject($heading ?? 'Happy Birthday!');

                if ($mediaUrl) {
                    try {
                        // Download file
                        $pdfContent = file_get_contents($mediaUrl);

                        if ($pdfContent !== false) {
                            // Save temporarily
                            $tempPath = storage_path('app/temp_invoice.pdf');
                            file_put_contents($tempPath, $pdfContent);

                            // Attach from local path
                            $message->attach($tempPath, [
                                'as'   => 'invoice.pdf',
                                'mime' => 'application/pdf',
                            ]);
                        } else {
                            echo "Failed to download file from: {$mediaUrl}\n";
                        }
                    } catch (\Exception $e) {
                        echo "Error attaching file: " . $e->getMessage() . "\n";
                    }
                }
            });
        }
    }
}
