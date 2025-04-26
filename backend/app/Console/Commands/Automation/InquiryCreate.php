<?php

namespace App\Console\Commands\Automation;

use App\Http\Controllers\Controller;
use App\Jobs\EmailSender;
use App\Jobs\WhatsappSender;
use App\Models\Template;
use Illuminate\Console\Command;

class InquiryCreate extends Command
{
    protected $signature = 'inquiry:create';

    protected $description = 'Send quotation to customer';

    protected $templates = [];

    protected $tags = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payload = [
            "command" => Template::INQUERY_CREATE,
            "heading" => "Inquiry",
            "company_id" => 1,
            "whatsapp" => "971554501483",
            "email" => "francisgill1000@gmail.com",

            "fields" => [
                'title' => "Mr",
                'full_name' => "francis",
                'from_date' => date("d-M-y"),
                'to_date' => date("d-M-y"),
                'room_type' => "castle",
            ]
        ];

        if ($payload["whatsapp"]) {
            WhatsappSender::dispatch([
                'recipient' => $payload["whatsapp"],
                'text' => (new Controller)->prepareMessage($payload['fields'], "whatsapp", $payload["command"]),
                'company_id' => $payload["company_id"],
            ]);
        }

        if ($payload["email"]) {
            EmailSender::dispatch([
                'recipient' => $payload["email"],
                'text' => (new Controller)->prepareMessage($payload['fields'], "email", $payload["command"]),
                'company_id' => $payload["company_id"],
                'heading' => $payload["heading"],
            ]);
        }

        echo json_encode($payload, JSON_PRETTY_PRINT);
    }
}
