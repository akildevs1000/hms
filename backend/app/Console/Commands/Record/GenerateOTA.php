<?php

namespace App\Console\Commands\Record;

use App\Models\Booking;
use App\Models\Record;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateOTA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:generate-daily-ota {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'record:generate-daily-ota for given company id based on current date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companyId = $this->argument("company_id");

        $date = date('Y-m-d', strtotime('yesterday')); // Use yesterday's date

        // Fetch bookings for the specified date range
        $bookingsGrouped = Booking::whereNotNull('source')
            ->where('booking_status', -1)
            ->whereDate('check_in', $date)
            ->get(['source', 'total_price as sum']) // Ensure 'Total Price' is in the selected fields
            ->groupBy('source')
            ->map(function ($group) {
                return [
                    "total_sum" => $group->sum('sum'),
                ];
            });


        $sourceData = [];

        $sources = Source::pluck("name")->toArray();

        foreach ($sources as $source) {
            $sourceData[$source] = [
                "total_sum" => '₹' . number_format(0, 2),
                "count" => 0,
            ];
        }

        // Output $bookingsGrouped for each source
        foreach ($bookingsGrouped as $source => $sum) {
            $sourceData[$source] = [
                "total_sum" => '₹' . number_format($sum['total_sum'], 2),
                "count" => count($bookingsGrouped[$source]),
            ];
        }

        $payload = [];

        $payload["date"] = $date;
        $payload["data"] = $sourceData;
        $payload["company_id"] = $companyId;
        $payload["type"] = Record::OTA;

        // Record::truncate();

        try {

            $auditRecord = Record::where("date", $date)
                ->where("type", Record::OTA)
                ->whereCompanyId($companyId)->first();

            if ($auditRecord) {
                $auditRecord->update($payload);
                Log::info(Record::OTA . " record updated for date: {$date}");
            } else {
                Record::create($payload);
                Log::info(Record::OTA . " record created for date: {$date}");
            }
        } catch (\Exception $e) {
            // Catch any exceptions and log an error message
            Log::error(Record::OTA . " Error processing date {$date}: " . $e->getMessage());
        }
    }
}
