<?php

namespace App\Console\Commands;

use App\Mail\AuditReportMail;
use App\Models\EmailNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAuditReportEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:audit_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an audit report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $date = date('Y-m-d', strtotime('yesterday')); // Use yesterday's date
            $data = EmailNotifications::with('company')->where('status', 1)->get(["company_id", "email", "whatsapp_number"]);

            foreach ($data as $value) {
                $data = [
                    'file' => storage_path("app/pdf/$date/$value->company_id/merged_file.pdf"),
                    'date' => date('Y-M-d H:i'),
                    'body' => 'Night Audit Report',
                    'company' => $value->company,
                ];

                if ($value->email) {
                    Mail::to($value)->send(new AuditReportMail($data));
                }
                // Logger::channel("custom")->error("Cron: $script_name. Night Audit mail sent to $value ");
            }
        } catch (\Exception $e) {
            $this->error('Failed to send audit report email: ' . $e->getMessage());
        }
    }
}
