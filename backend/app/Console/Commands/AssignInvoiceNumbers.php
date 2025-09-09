<?php
namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class AssignInvoiceNumbers extends Command
{
    protected $signature   = 'bookings:assign-invoices {start=1001}';
    protected $description = 'Assign sequential invoice numbers for each company, ordered by created_at';

    public function handle()
    {
        $start = (int) $this->argument('start');

        $this->info("Assigning invoice numbers starting from {$start} for each company...");

        // Get all companies that have bookings
        $companyIds = Booking::distinct()->pluck('company_id');

        foreach ($companyIds as $companyId) {
            // Start counter from the last assigned invoice or the given start number
            $lastInvoice = Booking::where('company_id', $companyId)->max('invoice_number');
            $counter     = $lastInvoice ? $lastInvoice + 1 : $start;

            $this->info("Processing company_id: {$companyId} (starting at {$counter})");

            $processedCount = 0; // Track number of bookings processed

            // Booking::where('company_id', $companyId)->update(['invoice_number' => null]);

            Booking::where('company_id', $companyId)
                ->whereNull('invoice_number') // skip already assigned

                ->where(function ($query) {
                    $query->whereNotNull('gst_number')
                        ->orWhereHas('customer', function ($q2) {
                            $q2->whereNotNull('gst_number');
                            $q2->orWhereHas('source', function ($q2) {
                                $q2->whereNotNull('gst');
                            });
                        });

                    ;
                })
                ->orderBy('created_at', 'asc')
                ->chunk(100, function ($bookings) use (&$counter, &$processedCount) {
                    foreach ($bookings as $booking) {
                        $booking->invoice_number = $counter++;
                        $booking->save();
                        $processedCount++;
                    }
                });

            $this->info("✅ Company {$companyId}: {$processedCount} bookings processed");
        }
    }
}
