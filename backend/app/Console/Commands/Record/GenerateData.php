<?php

namespace App\Console\Commands\Record;

use App\Models\AdminExpense;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateData extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:generate-data {company_id} {date} {isTest?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data with given company id and given starting date and with option test condition if test is true generating random values';

    public function __construct()
    {
        parent::__construct();
    }

    // Execute the console command.
    public function handle()
    {
        $companyId = $this->argument("company_id", 0);
        $dateInput = $this->argument('date'); // Directly using the passed date

        $isTest = $this->argument('isTest'); // Directly using the passed date


        $date = Carbon::parse($dateInput);

        // Get the start and end of the month
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Fetch the required data in one go (this will reduce database queries inside the loop)
        $bookings = Booking::query()
            ->where('company_id', $companyId)
            ->where('booking_status', Booking::CHECKED_IN)
            ->whereBetween('check_in', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get(["id", "booking_date", "check_in", "booking_status"]);

        // Prepare the payload values
        $ledger = (new Record)->getCityLedger($date, $companyId);
        $income = (new Record)->getAllIncome($date, $companyId);
        $expense = (new Record)->getExpense($date, $companyId);
        $paymentTypes = [
            'cash' => PaymentMode::CASH,
            'card' => PaymentMode::CARD,
            'online' => PaymentMode::ONLINE,
            'bank' => PaymentMode::BANK,
            'upi' => PaymentMode::UPI,
            'cheque' => PaymentMode::CHEQUE
        ];

        $data = [];

        foreach ($paymentTypes as $key => $paymentMode) {
            $data[$key] = (new Record)->getIncomeByPaymentType($date, $companyId, $paymentMode);
            $data["displayValues"][$key] = '₹' . number_format((new Record)->getIncomeByPaymentType($date, $companyId, $paymentMode), 2);
        }

        // Start processing each day
        $this->info("Processing all days of the month: {$startOfMonth->format('F Y')}");
        for ($currentDate = $startOfMonth; $currentDate <= $endOfMonth; $currentDate->addDay()) {

            // Count check-ins for the current day
            $checkInCount = $bookings->filter(function ($booking) use ($currentDate) {
                return $booking->check_in == $currentDate->format('Y-m-d');
            })->count();

            $data["sold"] = $checkInCount;
            $data["displayValues"]["sold"] = $checkInCount;

            $data["ledger"] = $ledger;
            $data["displayValues"]["ledger"] = '₹' . number_format($ledger, 2);

            $data["total"] = $income;
            $data["displayValues"]["total"] = '₹' . number_format($ledger, 2);

            $data["expense"] = $expense;
            $data["displayValues"]["expense"] = '₹' . number_format($ledger, 2);

            if ($isTest) {

                foreach ($paymentTypes as $key => $paymentMode) {
                    $value =  rand(1, 50);
                    $data[$key] = $value;
                    $data["displayValues"][$key] = '₹' . number_format($value, 2);
                }

                $sold = rand(1, 50);
                $ledger = rand(1, 50);
                $total = array_sum($data);
                $expense = rand(1, 50);

                $data["sold"] = $sold;
                $data["displayValues"]["sold"] = $sold;

                $data["ledger"] = $ledger;
                $data["displayValues"]["ledger"] = '₹' . number_format($ledger, 2);

                $data["total"] = $total;
                $data["displayValues"]["total"] = '₹' . number_format($total, 2);

                $data["expense"] = $expense;
                $data["displayValues"]["expense"] = '₹' . number_format($expense, 2);
            }

            $payload["date"] = $currentDate->format('Y-m-d');
            $payload["data"] = $data;
            $payload["company_id"] = $companyId;
            $payload["type"] = Record::SUMMARY;

            $this->info(json_encode($payload));


            try {
                // // Check if an audit record for this date already exists
                $auditRecord = Record::where("date", $currentDate->format('Y-m-d'))
                    ->whereCompanyId($companyId)
                    ->first();

                if ($auditRecord) {
                    $auditRecord->update($payload);
                    $this->info("Audit record updated for date: {$currentDate->format('Y-m-d')}");
                } else {
                    Record::create($payload);
                    $this->info("Audit record created for date: {$currentDate->format('Y-m-d')}");
                }
            } catch (\Exception $e) {
                // Catch any exceptions and log an error message
                $this->error("Error processing date {$currentDate->format('Y-m-d')}: " . $e->getMessage());
            }

            // Output the current date being processed
            $this->line($currentDate->format('Y-m-d'));
        }
    }
}
