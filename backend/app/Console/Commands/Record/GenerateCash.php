<?php

namespace App\Console\Commands\Record;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Record;
use Illuminate\Support\Facades\Log;

class GenerateCash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:generate-daily-cash {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    // Execute the console command.
    public function handle()
    {
        // Record::truncate();

        $companyId = $this->argument("company_id", 0);
        $date = date('Y-m-d', strtotime('yesterday')); // Use yesterday's date


        $bookings = Booking::query()
            ->where('booking_status', -1)
            ->where('company_id', $companyId)
            ->where('booking_status', Booking::CHECKED_IN)
            ->where('booking_date', $date)
            ->get(["id", "booking_date", "check_in", "booking_status"]);

        // Prepare the payload values
        $income = (new Record)->getAllIncome($date, $companyId);
        $expense = (new Record)->getExpense($date, $companyId);

        $data = [];



        // Count check-ins for the current day
        $checkInCount = $bookings->filter(function ($booking) use ($date) {
            return $booking->booking_date == $date && $booking->check_in && $booking->booking_status != -1;
        })->count();

        $data["sold"] = $checkInCount;
        $data["displayValues"]["sold"] = $checkInCount;


        $data["cash"] = $income;
        $data["displayValues"]["cash"] = '₹' . number_format($income, 2);

        $data["expense"] = $expense;
        $data["displayValues"]["expense"] = '₹' . number_format($expense, 2);

        $data["balance"] = $income - $expense;
        $data["displayValues"]["balance"] = '₹' . number_format($income - $expense, 2);

        $payload["date"] = $date;
        $payload["data"] = $data;
        $payload["company_id"] = $companyId;
        $payload["type"] = Record::CASH;

        try {

            $auditRecord = Record::where("date", $date)
                ->where("type", Record::CASH)
                ->whereCompanyId($companyId)->first();

            if ($auditRecord) {
                $auditRecord->update($payload);
                Log::info("Audit record updated for date: {$date}");
            } else {
                Record::create($payload);
                Log::info("Audit record created for date: {$date}");
            }
        } catch (\Exception $e) {
            // Catch any exceptions and log an error message
            Log::error("Error processing date {$date}: " . $e->getMessage());
        }
    }
}
