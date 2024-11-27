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

        $date = date("Y-m-d");

        // Fetch bookings for the specified date range
        $bookingsData = Booking::whereNotNull('source')
            ->whereDate('check_in', $date)
            ->selectRaw('DATE(check_in) as booking_date, source, COUNT(*) as count, SUM(total_price) as total_sum')
            ->groupBy('booking_date', 'source')
            ->get();

        $bookingsGrouped = $bookingsData
            ->groupBy('booking_date')
            ->map(function ($dayBookings) {
                return $dayBookings->keyBy('source');
            });

        $sourceData = [];

        $sources = Source::pluck("name")->toArray();

        // Initialize source data with default values
        foreach ($sources as $source) {
            $sourceData[$source] = [
                "total_sum" => '₹' . number_format(0, 2),
                "count" => 0,
            ];
        }

        // Populate data for the given date if bookings exist
        if ($bookingsGrouped->has($date)) {
            foreach ($bookingsGrouped[$date] as $source => $booking) {
                $sourceData[$source] = [
                    "total_sum" => '₹' . number_format($booking->total_sum, 2),
                    "count" => $booking->count,
                ];
            }
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
