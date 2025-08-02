<?php

namespace App\Jobs;

use App\Models\Company;
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
        $companyId = $this->request['company_id'] ?? null;

        if ($companyId) {
            $company = Company::find($companyId);

            if ($company) {
                // 🧹 (Optional) Clear previous mail config to avoid reuse across jobs
                app()['config']->offsetUnset('mail.mailers.smtp');

                // ✅ Dynamically apply the new config for this job
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $company->smtp_host,
                    'mail.mailers.smtp.port' => $company->smtp_port,
                    'mail.mailers.smtp.username' => $company->smtp_username,
                    'mail.mailers.smtp.password' => $company->smtp_password,
                    'mail.mailers.smtp.encryption' => $company->smtp_encryption,
                    'mail.from.address' => $company->from_email,
                    'mail.from.name' => $company->from_name,
                ]);
            }
        }

        $recipient   = $this->request['recipient'] ?? null;
        $messageBody = $this->request['text'] ?? null;
        $heading     = $this->request['heading'] ?? 'Notification';
        $mediaUrl    = $this->request['mediaUrl'] ?? null;

        if ($recipient && $messageBody) {
            // echo "\n" . lightDump(["Sending email to", $recipient]) . "\n";

            Mail::raw($messageBody, function ($message) use ($recipient, $heading, $mediaUrl) {
                $message->to($recipient)->subject($heading);

                if ($mediaUrl) {
                    $message->attach($mediaUrl);
                }
            });
        }
    }
}
