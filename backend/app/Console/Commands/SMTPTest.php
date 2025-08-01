<?php
namespace App\Console\Commands;

use App\Services\MailConfigService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SMTPTest extends Command
{
    protected $signature   = 'smtp:test {company_id} {email}';
    protected $description = 'Send a test SMTP email using company-specific mail settings';

    public function handle()
    {
        $companyId = $this->argument('company_id');

        $email = $this->argument('email');

        $this->info("⏳ Setting SMTP config for company ID: {$companyId}");

        (new MailConfigService)->setMailConfigForCompany($companyId);

        $config = [
            'default'      => config('mail.default'),
            'host'         => config('mail.mailers.smtp.host'),
            'port'         => config('mail.mailers.smtp.port'),
            'username'     => config('mail.mailers.smtp.username'),
            'password'     => config('mail.mailers.smtp.password'),
            'encryption'   => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name'    => config('mail.from.name'),
        ];

        $this->info(lightDump(["SMTP Settings Info:", $config]));

        try {
            Mail::raw('SMTP test email from local', function ($message) use ($email) {
                $message->to($email)->subject('SMTP Test!');
            });

            $this->info("✅ Test email sent successfully to {$email} using Company ID {$companyId}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
        }
    }
}
