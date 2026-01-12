<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Module;
use App\Models\TransactionNumberSeries;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignInvoiceNumbers extends Command
{
    protected $signature   = 'bookings:assign-invoices {--reset} {--date= : Only process bookings on or after this date (YYYY-MM-DD)}';
    protected $description = 'Assign sequential invoice numbers for each company, ordered by created_at';

    public function handle()
    {
        $companyIds = Booking::distinct()->pluck('company_id');

        $dateFilter = $this->option('date');

        foreach ($companyIds as $companyId) {

            $json = TransactionNumberSeries::whereCompanyId($companyId)->value("json") ?? [];
            $singleObject = (object) collect($json)->where(fn($q) => $q["module"] == Module::Invoice)->first();
            $start = $singleObject->starting_number ?? 1001;

            $this->recordLog("Assigning invoice numbers starting from {$start} for company {$companyId}...");

            $query = Booking::where('company_id', $companyId);
            if ($dateFilter) $query->where('created_at', '>=', $dateFilter);

            if ($this->option('reset')) {
                $this->recordLog("Resetting existing invoice numbers for company {$companyId}");
                $query->update(['invoice_number' => null, 'taxable_invoice_number' => null]);
            }

            // Get existing numbers for gap filling
            $existingInvoices = Booking::where('company_id', $companyId)
                ->whereNotNull('invoice_number')
                ->pluck('invoice_number')
                ->toArray();

            $existingTaxable = Booking::where('company_id', $companyId)
                ->whereNotNull('taxable_invoice_number')
                ->pluck('taxable_invoice_number')
                ->toArray();

            // Counter functions that fill gaps
            $counter = $start;
            $taxableCounter = $start;

            $getNextInvoiceNumber = function () use (&$counter, $existingInvoices) {
                while (in_array($counter, $existingInvoices)) {
                    $counter++;
                }
                $existingInvoices[] = $counter; // mark as used
                return $counter++;
            };

            $getNextTaxableNumber = function () use (&$taxableCounter, $existingTaxable) {
                while (in_array($taxableCounter, $existingTaxable)) {
                    $taxableCounter++;
                }
                $existingTaxable[] = $taxableCounter; // mark as used
                return $taxableCounter++;
            };

            $this->recordLog("Processing company_id: {$companyId}");

            $processedCount = 0;

            $queryBookings = Booking::where('company_id', $companyId)
                ->whereNull('invoice_number');

            if ($dateFilter) {
                $queryBookings->where('created_at', '>=', $dateFilter);
            }

            $queryBookings
                ->with("customer:id,gst_number,source_id")
                ->with("customer.source:id,gst")
                ->orderBy('created_at', 'asc')
                ->chunk(100, function ($bookings) use ($getNextInvoiceNumber, $getNextTaxableNumber, &$processedCount) {

                    foreach ($bookings as $booking) {

                        $booking->invoice_number = $getNextInvoiceNumber();

                        if ($booking->gst_number || $booking?->customer?->gst_number || $booking?->customer?->source?->gst) {
                            $booking->taxable_invoice_number = $getNextTaxableNumber();
                        }

                        $this->recordLog(
                            "Assigned Invoice #{$booking->invoice_number}" .
                                ($booking->taxable_invoice_number ? " & Taxable Invoice #{$booking->taxable_invoice_number}" : "") .
                                " to Booking ID {$booking->id}"
                        );

                        $booking->save();
                        $processedCount++;
                    }
                });

            $this->recordLog("✅ Company {$companyId}: {$processedCount} bookings processed");
        }
    }

    public function recordLog($msg = "Default Log Message"): void
    {
        $this->info($msg);
        Log::channel('invoice')->info($msg);
    }
}
