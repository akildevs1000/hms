<?php

namespace App\Services;

use App\Models\MailSetting;

class MailConfigService
{
    /**
     * Set mail config dynamically for a given company ID.
     *
     * @param int $companyId
     * @return void
     */
    public function setMailConfigForCompany(int $companyId): void
    {
        $settings = MailSetting::where('company_id', $companyId)->first();

        if (!$settings) {
            return;
        }

        config([
            'mail.default' => $settings->mailer ?? 'smtp',
            'mail.mailers.smtp.host' => $settings->host ?? 'smtp.gmail.com',
            'mail.mailers.smtp.port' => $settings->port ?? 587,
            'mail.mailers.smtp.username' => $settings->username ?? '',
            'mail.mailers.smtp.password' => $settings->password ?? '',
            'mail.mailers.smtp.encryption' => $settings->encryption ?? 'tls',
            'mail.from.address' => $settings->from_address ?? 'noreply@example.com',
            'mail.from.name' => $settings->from_name ?? config('app.name'),
        ]);
    }
}
