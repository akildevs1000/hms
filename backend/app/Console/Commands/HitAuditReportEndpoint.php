<?php

namespace App\Console\Commands;

use App\Models\AuditHistory;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon; // Import Carbon for date manipulation
use Illuminate\Support\Facades\Http;

class HitAuditReportEndpoint extends Command
{
    // The name and signature of the console command.
    protected $signature = 'audit:report {company_id}';

    // The console command description.
    protected $description = 'Hit the audit report API endpoint and retrieve data.';

    public function __construct()
    {
        parent::__construct();
    }

    // Execute the console command.
    public function handle()
    {
        $companyId = $this->argument('company_id');
        $fromDate = Carbon::yesterday()->format('Y-m-d'); // Format: YYYY-MM-DD
        $toDate = Carbon::today()->format('Y-m-d'); // Format: YYYY-MM-DD

        // Prepare the request URL
        $url = 'https://hms-backend.test/api/get_audit_report';

        try {

            $response = Http::withoutVerifying()->get($url, [
                'company_id' => $companyId,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]);


            if ($response->successful()) {

                $data = json_decode($response->getBody(), true);

                $arr = [
                    "type" => "---",
                    "file_name" => "---",
                    "file_path" => "---",
                    'data' => $data["data"],
                    'company_id' => $companyId,
                    'dateTime' => date("d M y h:i:s"),
                ];

                AuditHistory::create($arr);

                $this->info("Data for Audit History has been created");

                // create json file
                // $filePath = storage_path('app/audit_report_' . $companyId . '_' . $fromDate . '_' . $toDate . '.json');
                // file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

                // Notify the user that the file has been created
                // $this->info('The data has been saved to: ' . $filePath);
            } else {
                $this->error('success');
            }
        } catch (RequestException $e) {
            // Handle the exception
            $this->error('Error fetching data: ' . $e->getMessage());
        }
    }
}
