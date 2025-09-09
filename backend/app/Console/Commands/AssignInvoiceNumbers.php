<?php
namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignInvoiceNumbers extends Command
{
    protected $signature   = 'bookings:assign-invoices {start=1001}';
    protected $description = 'Assign sequential invoice numbers for each company, ordered by created_at';

    public function handle()
    {
        $start = (int) $this->argument('start');

        Log::channel('invoice')->info("Assigning invoice numbers starting from {$start} for each company...");

        $companyIds = Booking::distinct()->pluck('company_id');

        foreach ($companyIds as $companyId) {
            $lastInvoice = Booking::where('company_id', $companyId)->max('invoice_number');
            $counter     = $lastInvoice ? $lastInvoice + 1 : $start;

            Log::channel('invoice')->info("Processing company_id: {$companyId} (starting at {$counter})");

            $processedCount = 0;

            Booking::where('company_id', $companyId)
                ->whereNull('invoice_number')
                ->where(function ($query) {
                    $query->whereNotNull('gst_number')
                        ->orWhereHas('customer', function ($q2) {
                            $q2->whereNotNull('gst_number')
                               ->orWhereHas('source', function ($q3) {
                                   $q3->whereNotNull('gst');
                               });
                        });
                })
                ->orderBy('created_at', 'asc')
                ->chunk(100, function ($bookings) use (&$counter, &$processedCount) {
                    foreach ($bookings as $booking) {
                        $booking->invoice_number = $counter++;
                        $booking->save();
                        $processedCount++;
                    }
                });

            Log::channel('invoice')->info("✅ Company {$companyId}: {$processedCount} bookings processed");
        }
    }
}
