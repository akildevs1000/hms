<?php

namespace App\Console\Commands;

use App\Models\AdminExpense;
use App\Models\AuditFreeze;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Payment;
use App\Models\PaymentMode;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class ProcessAuditFreeze extends Command
{
    // The name and signature of the console command.
    protected $signature = 'app:process-audit-freeze';

    // The console command description.
    protected $description = 'Hit the audit report API endpoint and retrieve data.';

    public function __construct()
    {
        parent::__construct();
    }

    // Execute the console command.
    public function handle()
    {
        $companyIds = Company::pluck("id");

        foreach ($companyIds as $companyId) {
            $date = Carbon::yesterday()->format('Y-m-d');

            $bookingCounts = Booking::query()
                ->where('company_id', $companyId)
                ->where('booking_date', $date)
                ->get(["id", "booking_date", "check_in", "check_out", "booking_status"]);
            $payload = [
                "date" => $date,
                "check_in" => 0,
                "check_out" => 0,
                "day_use" => 0,
                "continue" => 0,
                "cancel" => 0,
                "booked" => 0,
                "breakfast" => $this->getBreakfast($date, $companyId),
                "ledger" => $this->getCityLedger($date, $companyId),
                "income" => $this->getIncome($date, $companyId),
                "expense" => $this->getExpense($date, $companyId),
                "cash_in_hand" => $this->getCashInHand($date, $companyId),
                "file" => "file",
                "company_id" => $companyId,
            ];

            foreach ($bookingCounts as $booking) {
                if ($date == $booking->check_in && $booking->booking_status != -1) {
                    ++$payload["check_in"];
                }
                if ($date == $booking->check_in && $booking->booking_status == 3) {
                    ++$payload["day_use"];
                }
                if ($date == $booking->check_out && $booking->booking_status != -1) {
                    ++$payload["check_out"];
                }

                if ($date == $booking->booking_date && $this->calculateDays([$booking->check_in, $booking->check_out]) > 1 && $booking->booking_status != -1) {
                    ++$payload["continue"];
                }

                if ($date == $booking->booking_date && $booking->booking_status != -1) {
                    ++$payload["booked"];
                }

                if ($date == $booking->booking_date && $booking->booking_status == -1) {
                    ++$payload["cancel"];
                }
            }
            $found = AuditFreeze::where("date", $date)->whereCompanyId($companyId)->first();

            if ($found) {
                $found->update($payload);
                $this->info("Data for Audit History has been created");
            }

            try {

                AuditFreeze::create($payload);

                $this->info("Data for Audit History has been created");

                // create json file
                // $filePath = storage_path('app/audit_report_' . $companyId . '_' . $fromDate . '_' . $toDate . '.json');
                // file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

                // Notify the user that the file has been created
                // $this->info('The data has been saved to: ' . $filePath);
            } catch (RequestException $e) {
                // Handle the exception
                $this->error('Error fetching data: ' . $e->getMessage());
            }
        }
    }

    public function getBreakfast($date, $companyId)
    {
        $FoodOrder = BookedRoom::where('company_id', $companyId)
            ->where(function ($query) use ($date, $companyId) {
                $query->whereDate('check_out', $date)
                    ->orWhereDate('check_in', $date);
            })
            ->whereIn('booking_status', [1, 2])
            ->selectRaw(
                "
            SUM(CASE WHEN DATE(check_out) = ? THEN breakfast ELSE 0 END) as expected_breakfast,
            SUM(CASE WHEN DATE(check_in) = ? THEN breakfast ELSE 0 END) as occupied_breakfast",
                [$date, $date]
            )
            ->first();

        $expectedBreakfast = $FoodOrder->expected_breakfast ?? 0;

        $occupiedBreakfast = $FoodOrder->occupied_breakfast ?? 0;

        return $expectedBreakfast + $occupiedBreakfast;
    }

    public function calculateDays($dates)
    {
        $startDate = new \DateTime($dates[0]);
        $endDate = new \DateTime($dates[1]);
        return $startDate->diff($endDate)->days;
    }

    public function getCityLedger($date, $companyId)
    {
        return Payment::query()
            ->where('is_city_ledger', 1)
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->sum("amount") ?? 0;
    }

    public function getIncome($date, $companyId)
    {
        return Payment::query()
            ->where('is_city_ledger', 0)
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->whereHas('paymentMode', fn($q) => $q->where('id', "!=", PaymentMode::CITYLEDGER))
            ->sum("amount") ?? 0;
    }

    public function getCashInHand($date, $companyId)
    {
        return Payment::query()
            ->where('is_city_ledger', 0)
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', '!=', -1);
            })
            ->whereHas('paymentMode', fn($q) => $q->where('id', PaymentMode::CASH))
            ->sum("amount") ?? 0;
    }

    public function getExpense($date, $companyId)
    {
        return AdminExpense::query()
            ->where('is_admin_expense', AdminExpense::NonManagementExpense)
            ->where('company_id', $companyId)
            ->whereDate('bill_date', $date ?? date("Y-m-d"))
            ->sum("total") ?? 0;
    }
}
