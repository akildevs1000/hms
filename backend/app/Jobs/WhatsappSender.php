<?php

namespace App\Jobs;

use App\Models\WhatsappClient;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WhatsappSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $request = $this->request;

        $request['clientId'] = $this->getClient($request["company_id"]);

        echo "\n" . json_encode($request, JSON_PRETTY_PRINT);

        Http::withoutVerifying()->post('https://wa.mytime2cloud.com/send-message', $request);
    }

    public function getClient($company_id)
    {
        $clientId = WhatsappClient::where("company_id", $company_id)->value("accounts")[0]["clientId"];
        return $clientId;
    }
}
