<?php
namespace App\Console\Commands;

use App\Http\Controllers\ReportGenerateController;
use App\Mail\AuditReportMail;
use App\Models\Company;
use App\Models\EmailNotifications;
use Illuminate\Console\Command;
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

            $company_ids = Company::orderBy('id', 'asc')->pluck("id");
            //$company_ids = [1, 2];
            foreach ($company_ids as $company_id) {

                $date = date('Y-m-d');

                $array = EmailNotifications::with(['report_type_access.report_type'])
                    ->where('company_id', $company_id)
                    ->where('status', 1)
                    ->where('email', '!=', '')
                    ->get(["email", "whatsapp_number"])->toArray();

                if (count($array)) {
                    (new ReportGenerateController())->processData($company_id, $date);
                }

                $folderPath = storage_path("app/public/pdf/$date/$company_id");

                $pdfFiles = glob("$folderPath/*.pdf");

                foreach ($array as $single) {

                    $email = $single["email"];

                    if (strpos($email, '@')) {
                        $this->info("Night Audit mail sent to " . $email);

                        $data = [
                            'files'   => $pdfFiles,
                            'date'    => date('Y-M-d H:i'),
                            'body'    => 'Night Audit Report',
                            'company' => Company::find($company_id),
                        ];

                        info(lightDump($data));

                        Mail::to($email = "francisgill1000@gmail.com")->queue(new AuditReportMail($data));
                        // Mail::to($email)->send(new AuditReportMail($data));
                    }


                    // if (in_array($company_id, array_column($reportResult, "company_id"))) {
                    //     foreach ($reportResult as $record) {
                    //         if ($record['company_id'] == $company_id) {
                    //             $this->info("Sdf");
                    //             $whatsappPayload = [
                    //                 'recipient' => $single["whatsapp_number"],
                    //                 'text'      => "testing",
                    //             ];

                    //             lightDump($whatsappPayload);

                    //             WhatsappSender::dispatch($whatsappPayload);
                    //             break; // stop once found
                    //         }
                    //     }
                    // }
                }

            }

        } catch (\Throwable $th) {
            echo "[" . $date . "] Cron: $script_name. Error occured while inserting logs.\n" . $th;
            Logger::channel("custom")->error("Cron: $script_name. Error Details: $th");
            return;
        }
    }
}
