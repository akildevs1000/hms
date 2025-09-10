<?php
namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Module;
use App\Models\TransactionNumberSeries;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignInvoiceNumbers extends Command
{
    protected $signature   = 'bookings:assign-invoices';
    protected $description = 'Assign sequential invoice numbers for each company, ordered by created_at';

    public function handle()
    {
        $json = TransactionNumberSeries::whereCompanyId(11)->value("json") ?? [];

        $singleObject = (object) collect($json)->where(fn($q) => $q["module"] == Module::Invoice)->first();

        $start = $singleObject->starting_number ?? 1001;

        $this->recordLog("Assigning invoice numbers starting from {$start} for each company...");

        $companyIds = Booking::distinct()->pluck('company_id');

        foreach ($companyIds as $companyId) {

            // Booking::where('company_id', $companyId)->update(["invoice_number" => null,"taxable_invoice_number" => null]);

            $lastInvoice = Booking::where('company_id', $companyId)->max('invoice_number');
            $counter     = $lastInvoice ? $lastInvoice + 1 : $start;

            $lastTaxableInvoice = Booking::where('company_id', $companyId)->max('taxable_invoice_number');
            $taxableCounter     = $lastTaxableInvoice ? $lastTaxableInvoice + 1 : $start;

            $this->recordLog("Processing company_id: {$companyId} (starting at {$counter})");
            $this->recordLog("Processing company_id: {$companyId} (starting Taxable at {$taxableCounter})");

            $processedCount = 0;

            Booking::where('company_id', $companyId)
                ->whereNull('invoice_number')
                ->with("customer:id,gst_number,source_id")
                ->with("customer.source:id,gst")
                ->orderBy('created_at', 'asc')
                ->chunk(100, function ($bookings) use (&$counter, &$taxableCounter, &$processedCount) {
                    foreach ($bookings as $booking) {

                        $booking->invoice_number = $counter++;

                        if ($booking->gst_number || $booking?->customer?->gst_number || $booking?->customer?->source?->gst) {
                            $booking->taxable_invoice_number = $taxableCounter++;
                        }

                        $this->recordLog("Assigned Invoice #{$booking->invoice_number}" . ($booking->taxable_invoice_number ? " & Taxable Invoice #{$booking->taxable_invoice_number}" : "") . " to Booking ID {$booking->id}");

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
