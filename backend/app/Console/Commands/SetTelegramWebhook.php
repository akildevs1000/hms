<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Set the Telegram webhook';

    private $botToken = '7356807670:AAGtb_m3juvOpUGZCBaMXK73oO7A0-iUPOg';
    private $webhookUrl = 'https://backend.myhotel2cloud.com/api/telegram-webhook'; // Your publicly accessible URL

    public function handle()
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/setWebhook";

        $response = Http::withoutVerifying()->post($url, [
            'url' => $this->webhookUrl,
        ]);

        if ($response->successful()) {
            $this->info('Webhook set successfully!');
        } else {
            $this->error('Failed to set webhook.');
        }

        return 0;
    }
}
