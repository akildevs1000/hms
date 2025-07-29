<?php
namespace App\Console\Commands;

use App\Http\Controllers\ReportGenerateController;
use App\Mail\AuditReportMail;
use App\Models\Company;
use App\Models\EmailNotifications;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Mail;

class AuditReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:generate_audit_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Audit Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $script_name = "GenerateAuditReport";

        $date = date("Y-m-d H:i:s");

        try {
            $reportGenerate = new ReportGenerateController();

            if (! $reportGenerate->generateAuditReport()) {
                echo "[" . $date . "] Cron: $script_name. cannot generated.\n";
                return;
            }

            $company_ids = Company::orderBy('id', 'asc')->pluck("id");
            //$company_ids = [1, 2];
            foreach ($company_ids as $company_id) {
                $date       = date('Y-m-d');
                $folderPath = storage_path("app/pdf/$date/$company_id");
                $pdfFiles   = glob("$folderPath/*.pdf");
                //return $pdfFiles;
                //$pdfFiles = storage_path("app/pdf/$date/$company_id/Today Checkin Report.pdf");

                // return $pdfFiles;

                $data = [
                    //'file' => $pdfFiles,
                    'files'   => $pdfFiles,
                    'date'    => date('Y-M-d H:i'),
                    'body'    => 'Night Audit Report',
                    'company' => Company::find($company_id),
                ];
                $emailsArray = EmailNotifications::with(['report_type_access.report_type'])
                    ->where('company_id', $company_id)
                    ->where('status', 1)
                    ->where('email', '!=', '')->pluck("email");

                foreach ($emailsArray as $email) {
                    if (strpos($email, '@')) {

                        // $email  = "francisgill1000@gmail.com";

                        $this->info("Cron: $script_name. Night Audit mail sent to $email ");

                        Mail::to("francisgill1000@gmail.com")->send(new AuditReportMail($data));

                        Logger::channel("custom")->error("Cron: $script_name. Night Audit mail sent to $email ");
                    }
                }
            }

            return;
        } catch (\Throwable $th) {
            echo "[" . $date . "] Cron: $script_name. Error occured while inserting logs.\n" . $th;
            Logger::channel("custom")->error("Cron: $script_name. Error Details: $th");
            return;
        }
    }
}
